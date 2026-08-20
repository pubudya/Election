document.addEventListener("DOMContentLoaded", async () => {
    const user = await App.requireSession();
    if (!user) {
        return;
    }
    App.renderShell("register", user);

    const [dentists, treatments] = await Promise.all([
        App.api("/api/dentists"),
        App.api("/api/treatments")
    ]);

    const dentistSelect = document.getElementById("dentistId");
    (dentists.data || []).forEach((dentist) => {
        const option = document.createElement("option");
        option.value = dentist.dentistId;
        option.textContent = `${dentist.dentistName} — ${dentist.specialization}`;
        dentistSelect.appendChild(option);
    });

    const treatmentSelect = document.getElementById("treatmentId");
    (treatments.data || []).forEach((treatment) => {
        const option = document.createElement("option");
        option.value = treatment.treatmentId;
        option.textContent = `${treatment.treatmentType} (${App.money(treatment.treatmentCost)})`;
        treatmentSelect.appendChild(option);
    });

    const dateInput = document.getElementById("appointmentDate");
    const today = new Date().toISOString().slice(0, 10);
    dateInput.min = today;
    dateInput.value = today;

    const timeSelect = document.getElementById("appointmentTime");
    for (let hour = 8; hour <= 18; hour++) {
        for (const minute of [0, 30]) {
            if (hour === 18 && minute === 30) {
                continue;
            }
            const value = `${String(hour).padStart(2, "0")}:${String(minute).padStart(2, "0")}`;
            const option = document.createElement("option");
            option.value = value;
            option.textContent = value;
            timeSelect.appendChild(option);
        }
    }

    document.getElementById("registerForm").addEventListener("submit", async (event) => {
        event.preventDefault();
        const payload = {
            patientName: document.getElementById("patientName").value,
            address: document.getElementById("address").value,
            contactNumber: document.getElementById("contactNumber").value,
            dentistId: Number(document.getElementById("dentistId").value),
            treatmentId: Number(document.getElementById("treatmentId").value),
            appointmentDate: document.getElementById("appointmentDate").value,
            appointmentTime: document.getElementById("appointmentTime").value,
            notes: document.getElementById("notes").value
        };
        const result = await App.api("/api/appointments", {
            method: "POST",
            body: JSON.stringify(payload)
        });
        App.showAlert("registerAlert", result.message, result.success);
        if (result.success) {
            document.getElementById("registerForm").reset();
            dateInput.value = today;
            App.toast("Saved " + result.data.appointmentNumber);
        }
    });
});

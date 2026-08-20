document.addEventListener("DOMContentLoaded", async () => {
    const user = await App.requireSession();
    if (!user) {
        return;
    }
    App.renderShell("search", user);

    async function loadAll() {
        const result = await App.api("/api/appointments");
        renderTable(result.data || []);
    }

    function renderDetails(item) {
        document.getElementById("details").innerHTML = `
            <div class="grid-2">
                <p><strong>Appointment No.</strong><br>${App.escape(item.appointmentNumber)}</p>
                <p><strong>Status</strong><br>${App.badge(item.status)}</p>
                <p><strong>Patient</strong><br>${App.escape(item.patientName)}</p>
                <p><strong>Contact</strong><br>${App.escape(item.contactNumber)}</p>
                <p><strong>Address</strong><br>${App.escape(item.address)}</p>
                <p><strong>Dentist</strong><br>${App.escape(item.dentistName)} (${App.escape(item.specialization)})</p>
                <p><strong>Treatment</strong><br>${App.escape(item.treatmentType)}</p>
                <p><strong>Date & time</strong><br>${App.escape(item.appointmentDate)} at ${App.escape(item.appointmentTime)}</p>
            </div>
            <div class="actions">
                <a class="btn btn-primary" style="width:auto;text-align:center" href="bill.html?number=${item.appointmentNumber}">Calculate bill</a>
                ${item.status !== "CANCELLED" ? `<button class="btn btn-danger" id="cancelBtn" type="button">Cancel appointment</button>` : ""}
            </div>
        `;
        const cancelBtn = document.getElementById("cancelBtn");
        if (cancelBtn) {
            cancelBtn.addEventListener("click", async () => {
                if (!confirm("Cancel this appointment and free the time slot?")) {
                    return;
                }
                const result = await App.api("/api/appointments", {
                    method: "PUT",
                    body: JSON.stringify({ appointmentNumber: item.appointmentNumber, status: "CANCELLED" })
                });
                App.showAlert("searchAlert", result.message, result.success);
                if (result.success) {
                    document.getElementById("searchForm").dispatchEvent(new Event("submit"));
                    loadAll();
                }
            });
        }
    }

    function renderTable(items) {
        document.getElementById("listBody").innerHTML = items.map((item) => `
            <tr>
                <td>${App.escape(item.appointmentNumber)}</td>
                <td>${App.escape(item.patientName)}</td>
                <td>${App.escape(item.dentistName)}</td>
                <td>${App.escape(item.appointmentDate)} ${App.escape(item.appointmentTime)}</td>
                <td>${App.badge(item.status)}</td>
            </tr>
        `).join("") || `<tr><td colspan="5">No appointments yet.</td></tr>`;
    }

    document.getElementById("searchForm").addEventListener("submit", async (event) => {
        event.preventDefault();
        const number = document.getElementById("appointmentNumber").value.trim().toUpperCase();
        const result = await App.api("/api/appointments?number=" + encodeURIComponent(number));
        App.showAlert("searchAlert", result.message, result.success);
        if (result.success) {
            renderDetails(result.data);
        } else {
            document.getElementById("details").innerHTML = "";
        }
    });

    await loadAll();
});

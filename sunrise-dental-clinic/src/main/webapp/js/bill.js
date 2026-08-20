document.addEventListener("DOMContentLoaded", async () => {
    const user = await App.requireSession();
    if (!user) {
        return;
    }
    App.renderShell("bill", user);

    const params = new URLSearchParams(window.location.search);
    if (params.get("number")) {
        document.getElementById("appointmentNumber").value = params.get("number");
    }

    document.getElementById("previewForm").addEventListener("submit", async (event) => {
        event.preventDefault();
        await previewBill();
    });

    document.getElementById("generateBtn").addEventListener("click", async () => {
        const number = document.getElementById("appointmentNumber").value.trim().toUpperCase();
        const result = await App.api("/api/bills", {
            method: "POST",
            body: JSON.stringify({ appointmentNumber: number })
        });
        App.showAlert("billAlert", result.message, result.success);
        if (result.success) {
            renderReceipt(result.data);
        }
    });

    document.getElementById("printBtn").addEventListener("click", () => window.print());

    if (params.get("number")) {
        await previewBill();
    }

    async function previewBill() {
        const number = document.getElementById("appointmentNumber").value.trim().toUpperCase();
        const result = await App.api("/api/bills?number=" + encodeURIComponent(number));
        App.showAlert("billAlert", result.message, result.success);
        if (!result.success) {
            document.getElementById("receipt").classList.add("hidden");
            return;
        }
        renderReceipt(result.data);
    }

    function renderReceipt(data) {
        const appt = data.appointment;
        const bill = data.bill;
        document.getElementById("receipt").classList.remove("hidden");
        document.getElementById("rBillNo").textContent = bill ? bill.billNumber : "Preview";
        document.getElementById("rApptNo").textContent = appt.appointmentNumber || "";
        document.getElementById("rPatient").textContent = appt.patientName || "";
        document.getElementById("rAddress").textContent = appt.address || "";
        document.getElementById("rPhone").textContent = appt.contactNumber || "";
        document.getElementById("rDentist").textContent = appt.dentistName || "";
        document.getElementById("rTreatment").textContent = appt.treatmentType || "";
        document.getElementById("rWhen").textContent = (appt.appointmentDate || "") + " at " + (appt.appointmentTime || "");
        document.getElementById("rConsult").textContent = App.money(data.consultationFee);
        document.getElementById("rTreat").textContent = App.money(data.treatmentCost);
        document.getElementById("rTotal").textContent = App.money(data.totalAmount);
        document.getElementById("rStatus").textContent = bill ? bill.paymentStatus : "NOT SAVED";
    }
});

document.addEventListener("DOMContentLoaded", async () => {
    const user = await App.requireSession();
    if (!user) {
        return;
    }
    App.renderShell("dashboard", user);

    const result = await App.api("/api/dashboard");
    if (!result.success) {
        App.toast(result.message);
        return;
    }
    const data = result.data;
    document.getElementById("statToday").textContent = data.todayCount;
    document.getElementById("statScheduled").textContent = data.scheduledCount;
    document.getElementById("statCompleted").textContent = data.completedCount;
    document.getElementById("statRevenue").textContent = App.money(data.todayRevenue);

    const rows = (data.todayAppointments || []).map((item) => `
        <tr>
            <td>${App.escape(item.appointmentNumber)}</td>
            <td>${App.escape(item.patientName)}</td>
            <td>${App.escape(item.dentistName)}</td>
            <td>${App.escape(item.treatmentType)}</td>
            <td>${App.escape(item.appointmentTime)}</td>
            <td>${App.badge(item.status)}</td>
        </tr>
    `).join("");
    document.getElementById("todayBody").innerHTML = rows ||
        `<tr><td colspan="6">No appointments scheduled for today.</td></tr>`;
});

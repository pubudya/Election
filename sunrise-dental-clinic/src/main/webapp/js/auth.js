document.addEventListener("DOMContentLoaded", async () => {
    const session = await App.api("/api/session");
    if (session.success) {
        window.location.href = App.url("/dashboard.html");
        return;
    }

    const form = document.getElementById("loginForm");
    form.addEventListener("submit", async (event) => {
        event.preventDefault();
        const username = document.getElementById("username").value.trim();
        const password = document.getElementById("password").value;
        const result = await App.api("/api/login", {
            method: "POST",
            body: JSON.stringify({ username, password })
        });
        if (!result.success) {
            App.showAlert("loginAlert", result.message, false);
            return;
        }
        window.location.href = App.url("/dashboard.html");
    });
});

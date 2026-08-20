const App = {
    context() {
        const path = window.location.pathname;
        return path.substring(0, path.lastIndexOf("/"));
    },

    url(path) {
        return this.context() + path;
    },

    async api(path, options = {}) {
        const response = await fetch(this.url(path), {
            credentials: "same-origin",
            headers: { "Content-Type": "application/json", ...(options.headers || {}) },
            ...options
        });
        const payload = await response.json().catch(() => ({
            success: false,
            message: "The server returned an unexpected response."
        }));
        if (response.status === 401 && !path.includes("/api/login") && !path.includes("/api/session")) {
            window.location.href = this.url("/index.html");
        }
        return payload;
    },

    toast(message) {
        let el = document.getElementById("toast");
        if (!el) {
            el = document.createElement("div");
            el.id = "toast";
            el.className = "toast";
            document.body.appendChild(el);
        }
        el.textContent = message;
        el.classList.add("show");
        setTimeout(() => el.classList.remove("show"), 3200);
    },

    showAlert(id, message, ok) {
        const el = document.getElementById(id);
        if (!el) {
            return;
        }
        el.textContent = message;
        el.className = "alert show " + (ok ? "alert-ok" : "alert-error");
    },

    escape(value) {
        return String(value ?? "").replace(/[&<>"']/g, (char) => ({
            "&": "&amp;",
            "<": "&lt;",
            ">": "&gt;",
            '"': "&quot;",
            "'": "&#39;"
        }[char]));
    },

    money(value) {
        return "LKR " + Number(value || 0).toLocaleString("en-LK", {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    },

    badge(status) {
        const cls = {
            SCHEDULED: "badge-scheduled",
            COMPLETED: "badge-completed",
            CANCELLED: "badge-cancelled"
        }[status] || "";
        return `<span class="badge ${cls}">${status}</span>`;
    },

    async requireSession() {
        const result = await this.api("/api/session");
        if (!result.success) {
            window.location.href = this.url("/index.html");
            return null;
        }
        return result.data;
    },

    renderShell(active, user) {
        const app = document.getElementById("app");
        const page = document.getElementById("page-content").innerHTML;
        app.innerHTML = `
            <aside class="sidebar">
                <div class="brand">
                    <div class="brand-mark">☀</div>
                    <div>
                        <strong>Sunrise Dental</strong>
                        <small>Colombo Clinic</small>
                    </div>
                </div>
                <nav class="nav">
                    <a href="dashboard.html" class="${active === "dashboard" ? "active" : ""}">Dashboard</a>
                    <a href="register.html" class="${active === "register" ? "active" : ""}">New Appointment</a>
                    <a href="search.html" class="${active === "search" ? "active" : ""}">Find Appointment</a>
                    <a href="bill.html" class="${active === "bill" ? "active" : ""}">Calculate Bill</a>
                    <a href="help.html" class="${active === "help" ? "active" : ""}">Help</a>
                </nav>
                <div class="sidebar-foot">
                    Open 8:00 AM – 6:00 PM<br>Galle Road, Colombo
                </div>
            </aside>
            <main class="content">
                <div class="topbar">
                    <div>
                        <div class="eyebrow">Sunrise Dental Clinic</div>
                        <h1 class="display" style="margin:0">${document.title.replace(" | Sunrise Dental Clinic", "")}</h1>
                    </div>
                    <div class="user-chip">
                        <div>
                            <strong>${App.escape(user.fullName)}</strong><br>
                            <small>${App.escape(user.role)}</small>
                        </div>
                        <button class="btn btn-ghost" id="logoutBtn" type="button">Exit</button>
                    </div>
                </div>
                ${page}
            </main>
        `;
        document.getElementById("logoutBtn").addEventListener("click", async () => {
            await this.api("/api/logout", { method: "POST", body: "{}" });
            window.location.href = this.url("/index.html");
        });
    }
};

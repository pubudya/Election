document.addEventListener("DOMContentLoaded", async () => {
    const user = await App.requireSession();
    if (!user) {
        return;
    }
    App.renderShell("help", user);
});

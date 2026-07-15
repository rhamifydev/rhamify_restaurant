document.addEventListener("DOMContentLoaded", () => {
    if (!window.Echo || !window.loggedInUserId || document.querySelector("#mychatbox")) {
        return;
    }

    window.Echo.private(`chat.${window.loggedInUserId}`).listen("ChatEvent", () => {
        $(".message-envelope").addClass("beep");
    });
});

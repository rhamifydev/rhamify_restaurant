document.addEventListener("DOMContentLoaded", () => {
    if (!window.Echo || !window.loggedInUserId || document.querySelector("#v-pills-message")) {
        return;
    }

    window.Echo.private(`chat.${window.loggedInUserId}`).listen("ChatEvent", () => {
        let currentCount = Number($(".sunseen-message-count").first().text()) || 0;
        $(".sunseen-message-count").text(currentCount + 1);
    });
});

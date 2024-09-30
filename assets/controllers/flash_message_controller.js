import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static targets = ["message"];

    connect() {
        this.autoClose();
    }

    close(event) {
        const messageElement = event.currentTarget.closest("[data-flash-message-target='message']");
        if (messageElement) {
            messageElement.remove();
        }
    }

    autoClose() {
        this.messageTargets.forEach((message) => {
            setTimeout(() => {
                message.remove();
            }, 5000);
        });
    }
}

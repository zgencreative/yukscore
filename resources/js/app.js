import "./bootstrap.js";
import "./player.js";
import { createApp } from "vue";

import ChatComponent from "./components/ChatComponent.vue";

import Alpine from "alpinejs";

window.Alpine = Alpine;

Alpine.start();

const chatAppElement = document.getElementById("chat-app");
if (chatAppElement) {
    const props = {
        matchId: chatAppElement.dataset.matchId,
    };
    createApp(ChatComponent, props).mount(chatAppElement);
}

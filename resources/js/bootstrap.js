// resources/js/bootstrap.js

import axios from "axios";
window.axios = axios;

window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

/**
 * ===================================================================
 * BAGIAN PENTING YANG HILANG ADA DI SINI
 * ===================================================================
 * Kode ini akan mengambil CSRF token dari meta tag di HTML Anda
 * dan secara otomatis melampirkannya ke semua request Axios.
 * Ini akan menyelesaikan error 419 Page Expired.
 */
let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common["X-CSRF-TOKEN"] = token.content;
    window.axios.defaults.headers["Access-Control-Allow-Origin"] = "*";
} else {
    console.error(
        "CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token"
    );
}

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allow your team to quickly build robust real-time web applications.
 */
import Echo from "laravel-echo";
import Pusher from "pusher-js";

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: "pusher",
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? "mt1",
    forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? "https") === "https",
});

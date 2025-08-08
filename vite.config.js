import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "/public/css/style.css",
                "/public/js/script.js",
                "resources/js/app.js",
            ],
            refresh: true,
        }),
        vue(),
    ],
    server: {
        host: "0.0.0.0",
        hmr: {
            host: "192.168.18.31",
        },
        cors: {
            origin: "*",
        },
    },
});

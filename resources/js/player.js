document.addEventListener("DOMContentLoaded", async function () {
    // --- Konfigurasi Awal ---
    const streamDataElement = document.getElementById("stream-data");

    const hlsStreams = JSON.parse(streamDataElement.dataset.hlsStreams);
    const iframeStreams = JSON.parse(streamDataElement.dataset.iframeStreams);
    const dashStreams = JSON.parse(streamDataElement.dataset.dashStreams);
    const tvLinks = JSON.parse(streamDataElement.dataset.tvLinks);
    const proxyBaseUrl = JSON.parse(streamDataElement.dataset.proxyBaseUrl);
    let streamManifestMap = {};

    // --- Seleksi Elemen DOM ---
    const hlsPlayerElement = document.getElementById("hls-player");
    const iframePlayerElement = document.getElementById("iframe-player");
    const dashPlayerElement = document.getElementById("dash-player");
    const playerTypeControls = document.getElementById("player-type-controls");
    const hlsServerControls = document.getElementById("hls-server-controls");
    const iframeServerControls = document.getElementById(
        "iframe-server-controls"
    );
    const dashServerControls = document.getElementById("dash-server-controls");

    // --- Fungsi Fetch Data ---
    async function fetchStreamManifest() {
        try {
            // <<< PERUBAHAN: Menggunakan proxy lokal untuk menghindari CORS
            const response = await fetch("/proxy/streams");
            if (!response.ok)
                throw new Error(`HTTP error! status: ${response.status}`);
            const data = await response.json();
            data.forEach((item) => {
                streamManifestMap[item.id] = item;
            });
            console.log("Manifest stream berhasil dimuat via proxy.");
        } catch (error) {
            console.error("Gagal memuat manifest stream:", error);
            document.querySelectorAll("[data-tvlink-id]").forEach((btn) => {
                btn.disabled = true;
                btn.style.backgroundColor = "#555";
                btn.textContent += " (Error)";
            });
        }
    }

    await fetchStreamManifest();

    // --- Cek Ketersediaan & Inisialisasi Player ---
    const isAnyStreamAvailable =
        hlsStreams.length > 0 ||
        iframeStreams.length > 0 ||
        dashStreams.length > 0 ||
        tvLinks.length > 0;
    if (!isAnyStreamAvailable) {
        document.querySelector(".video-wrapper").innerHTML =
            '<p style="text-align:center; padding: 40px; color: #ffcccc;">Stream tidak ditemukan.</p>';
        return;
    }

    let hlsPlayer, dashPlayerInstance;
    if (hlsStreams.length > 0) {
        hlsPlayer = videojs(hlsPlayerElement, {
            autoplay: true,
            controls: true,
            responsive: true,
        });
    }
    if (dashStreams.length > 0 || tvLinks.length > 0) {
        dashPlayerInstance = new shaka.Player(dashPlayerElement);
        dashPlayerElement.autoplay = true;
        dashPlayerElement.controls = true;
        dashPlayerInstance.addEventListener("error", (e) =>
            console.error("Shaka Player Error:", e.detail)
        );
    }

    // --- FUNGSI-FUNGSI UTAMA ---
    function loadHlsStream(url) {
        if (!hlsPlayer) return;
        const proxiedUrl = url.includes("su.carryflix.workers.dev")
            ? `${proxyBaseUrl}/hls?url=${encodeURIComponent(url)}`
            : url;
        hlsPlayer.src({ src: proxiedUrl, type: "application/x-mpegURL" });
    }

    function loadDashStream(url, drmData = null) {
        if (!dashPlayerInstance) return;

        console.log("Memuat DASH Stream:", url);
        let drmConfiguration = {}; // Default: tanpa DRM

        if (drmData) {
            if (drmData.widevine) {
                console.log("Mengonfigurasi DRM Widevine");
                drmConfiguration = {
                    servers: { "com.widevine.alpha": drmData.widevine },
                };
            } else if (drmData.clearkeys || drmData.clearkey) {
                console.log("Mengonfigurasi DRM ClearKey");
                drmConfiguration = {
                    clearKeys: drmData.clearkeys || drmData.clearkey,
                };
            }
        }

        console.log("Konfigurasi Shaka Player:", { drm: drmConfiguration });
        dashPlayerInstance.configure({ drm: drmConfiguration });
        dashPlayerInstance
            .load(url)
            .catch((e) => console.error("Error loading DASH stream:", e));
    }

    function loadIframeStream(url) {
        iframePlayerElement.src = url;
    }

    function switchPlayerView(playerType) {
        const playerMap = {
            hls: { player: hlsPlayerElement, controls: hlsServerControls },
            dash: { player: dashPlayerElement, controls: dashServerControls },
            iframe: {
                player: iframePlayerElement,
                controls: iframeServerControls,
            },
        };
        Object.values(playerMap).forEach(({ player, controls }) => {
            player?.classList.add("hidden");
            controls?.classList.add("hidden");
        });
        if (hlsPlayer) hlsPlayer.pause();
        if (dashPlayerInstance?.getMediaElement())
            dashPlayerInstance.getMediaElement().pause();
        iframePlayerElement.src = "about:blank";
        const selected = playerMap[playerType];
        if (selected) {
            selected.player?.classList.remove("hidden");
            selected.controls?.classList.remove("hidden");
        }
    }

    // --- EVENT LISTENERS ---
    function setupButtonActivation(controlsContainer, clickedButton) {
        if (!controlsContainer) return;
        controlsContainer
            .querySelectorAll(".server-btn")
            .forEach((btn) => btn.classList.remove("active"));
        clickedButton?.classList.add("active");
    }

    if (hlsServerControls) {
        hlsServerControls.addEventListener("click", (e) => {
            const button = e.target.closest("button[data-src]");
            if (!button) return;
            loadHlsStream(button.dataset.src);
            setupButtonActivation(hlsServerControls, button);
        });
    }

    if (iframeServerControls) {
        iframeServerControls.addEventListener("click", (e) => {
            const button = e.target.closest("button[data-src]");
            if (!button) return;
            loadIframeStream(button.dataset.src);
            setupButtonActivation(iframeServerControls, button);
        });
    }

    if (dashServerControls) {
        dashServerControls.addEventListener("click", (e) => {
            const button = e.target.closest("button");
            if (!button) return;

            if (button.dataset.tvlinkId) {
                const streamId = button.dataset.tvlinkId;
                const streamData = streamManifestMap[streamId];
                if (streamData) {
                    loadDashStream(streamData.url, streamData.drm || null);
                } else {
                    alert(`Stream untuk ${streamId} tidak dapat dimuat.`);
                }
            } else if (button.dataset.src) {
                loadDashStream(button.dataset.src, null);
            }
            setupButtonActivation(dashServerControls, button);
        });
    }

    if (playerTypeControls) {
        playerTypeControls.addEventListener("click", (e) => {
            const button = e.target.closest("button[data-player-type]");
            if (!button) return;
            switchPlayerView(button.dataset.playerType);
            setupButtonActivation(playerTypeControls, button);
        });
    }

    // --- LOGIKA INISIALISASI ---
    const hlsAvailable = hlsStreams.length > 0;
    const dashAvailable = dashStreams.length > 0 || tvLinks.length > 0;
    const iframeAvailable = iframeStreams.length > 0;

    if (hlsAvailable) {
        switchPlayerView("hls");
        playerTypeControls?.querySelector('[data-player-type="hls"]')?.click();
        hlsServerControls.querySelector(".server-btn")?.click();
    } else if (dashAvailable) {
        switchPlayerView("dash");
        playerTypeControls?.querySelector('[data-player-type="dash"]')?.click();
        dashServerControls.querySelector(".server-btn")?.click();
    } else if (iframeAvailable) {
        switchPlayerView("iframe");
        playerTypeControls
            ?.querySelector('[data-player-type="iframe"]')
            ?.click();
        iframeServerControls.querySelector(".server-btn")?.click();
    }
});

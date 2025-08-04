document.addEventListener("DOMContentLoaded", async function () {
    // --- Konfigurasi Awal ---
    let hlsPlayer, dashPlayerInstance;
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

    function setupLoginModalTimer() {
        // 1. Cek status login dari body tag
        const isLoggedIn = document.body.dataset.isLoggedIn === "true";
        console.log(sessionStorage.getItem("loginModalShown"));

        // 2. Jika pengguna adalah 'guest' DAN modal belum pernah ditampilkan di sesi ini
        if (!isLoggedIn) {
            const totalDuration = 30000; // Total durasi 30 detik

            // 3. Cek apakah waktu mulai sudah tersimpan di sessionStorage
            let countdownStartTime =
                sessionStorage.getItem("countdownStartTime");

            if (!countdownStartTime) {
                // Jika belum ada, ini adalah kunjungan pertama. Catat waktu sekarang.
                countdownStartTime = Date.now();
                sessionStorage.setItem(
                    "countdownStartTime",
                    countdownStartTime
                );
                console.log("Timer dimulai untuk pertama kali.");
            }

            // 4. Hitung waktu yang sudah berlalu
            const elapsedTime = Date.now() - parseInt(countdownStartTime, 10);

            // 5. Hitung sisa waktu
            const timeRemaining = totalDuration - elapsedTime;

            console.log(
                `Waktu tersisa untuk modal: ${Math.round(
                    timeRemaining / 1000
                )} detik.`
            );

            // 6. Jalankan aksi jika waktunya sudah habis atau kurang dari 0
            if (timeRemaining <= 0) {
                // Jika waktu sudah habis (misalnya pengguna refresh setelah 30 detik)
                triggerModalActions();
            } else {
                // Jika masih ada sisa waktu, jalankan setTimeout dengan sisa waktu tersebut
                setTimeout(triggerModalActions, timeRemaining);
            }
        }
    }

    // Pisahkan aksi ke dalam fungsi sendiri agar tidak duplikat kode
    function triggerModalActions() {
        console.log("Waktu habis. Menampilkan modal login.");

        // Pause player yang sedang aktif
        if (typeof hlsPlayer !== "undefined" && hlsPlayer.hasStarted()) {
            hlsPlayer.pause();
        }
        if (
            typeof dashPlayerInstance !== "undefined" &&
            dashPlayerInstance.getMediaElement() &&
            !dashPlayerInstance.getMediaElement().paused
        ) {
            dashPlayerInstance.getMediaElement().pause();
        }
        if (
            iframePlayerElement &&
            !iframePlayerElement.classList.contains("hidden")
        ) {
            // Mengosongkan src akan menghentikan video di dalam iframe
            iframePlayerElement.src = "about:blank";
        }

        const allPlayerControls =
            document.getElementsByClassName("controls-group");

        // Loop melalui setiap elemen dan tambahkan class 'controls-disabled'
        for (const controlGroup of allPlayerControls) {
            controlGroup.classList.add("controls-disabled");
        }

        // Panggil fungsi showModal() Anda
        showModal();

        // Set flag di sessionStorage agar modal tidak muncul lagi
        sessionStorage.setItem("loginModalShown", "true");
    }

    // Jangan lupa panggil fungsi utamanya
    setupLoginModalTimer();

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
        const proxiedUrl = url.includes("carryflix.workers.dev")
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

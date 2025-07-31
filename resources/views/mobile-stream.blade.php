<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Stream Player</title>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/shaka-player@4.7.13/dist/controls.css" />
    <script src="https://cdn.jsdelivr.net/npm/shaka-player@4.7.13/dist/shaka-player.ui.js"></script>

    <link href="https://vjs.zencdn.net/8.11.8/video-js.css" rel="stylesheet" />
    <script src="https://vjs.zencdn.net/8.11.8/video.min.js"></script>

    <style>
        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            background-color: #000;
            color: white;
            font-family: sans-serif;
        }
        #player-container {
            width: 100%;
            height: 100%;
        }
        #player-container > * {
            width: 100%;
            height: 100%;
            border: none;
        }
    </style>
</head>

<body>
    <div id="player-container">
        </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let shakaPlayer = null;
            let hlsPlayer = null;
            let manifestPromise = null;

            const container = document.getElementById('player-container');
            const proxyBaseUrl = 'https://apistream.ngrok.app';

            function showError(message) {
                container.innerHTML = `<div style="text-align:center; padding: 40px; box-sizing: border-box;">${message}</div>`;
            }

            // --- FUNGSI IFRAME (YANG DITAMBAHKAN KEMBALI) ---
            function playIframeStream(url) {
                console.log(`▶️ Memuat Iframe: ${url}`);
                // Hentikan player lain jika sedang aktif
                if (shakaPlayer) shakaPlayer.unload();
                if (hlsPlayer) hlsPlayer.pause();
                
                container.innerHTML = `<iframe src="${url}" frameborder="0" allow="encrypted-media" allowfullscreen></iframe>`;
            }

            // --- Fungsi Player HLS (Video.js) ---
            function loadHlsStream(url) {
                if (hlsPlayer === null) {
                    console.log("🚀 Inisialisasi pemutar HLS (Video.js)...");
                    container.innerHTML = '<video id="hls-player" class="video-js vjs-default-skin" controls autoplay></video>';
                    hlsPlayer = videojs('hls-player');
                }
                if (shakaPlayer) shakaPlayer.unload();
                
                const proxiedUrl = url.includes('su.carryflix.workers.dev') ? `${proxyBaseUrl}/hls?url=${encodeURIComponent(url)}` : url;
                console.log(`▶️ Memuat HLS Stream: ${proxiedUrl}`);
                hlsPlayer.src({ src: proxiedUrl, type: 'application/x-mpegURL' });
            }

            // --- Fungsi Player DASH (Shaka Player) ---
            async function playDashStream(url, drmData = null) {
                if (shakaPlayer === null) {
                    console.log("🚀 Inisialisasi pemutar DASH (Shaka)...");
                    container.innerHTML = '<video id="dash-player" controls autoplay></video>';
                    const videoElement = document.getElementById('dash-player');
                    shakaPlayer = new shaka.Player(videoElement);
                    videoElement.autoplay = true;
                    videoElement.controls = true;
                    shakaPlayer.addEventListener("error", (e) => console.error("Shaka Player Error:", e.detail));
                }
                if (hlsPlayer) hlsPlayer.pause();

                let drmConfiguration = {};
                if (drmData) {
                    if (drmData.widevine) drmConfiguration = { servers: { 'com.widevine.alpha': drmData.widevine } };
                    else if (drmData.clearkey) drmConfiguration = { clearKeys: drmData.clearkey };
                }
                
                try {
                    shakaPlayer.configure({ drm: drmConfiguration });
                    await shakaPlayer.load(url);
                    console.log(`✅ Stream DASH berhasil dimuat: ${url}`);
                } catch (error) {
                    showError(`Gagal memuat stream.<br>(${error.message})`);
                }
            }
            
            // --- Fungsi Player TV (Mencari di Manifest) ---
            function playTvStream(streamId) {
                if (manifestPromise === null) {
                    manifestPromise = fetch("/proxy/streams")
                        .then(response => {
                            if (!response.ok) throw new Error(`Gagal mengambil data: ${response.status}`);
                            return response.json();
                        })
                        .catch(error => {
                            manifestPromise = null;
                            throw error;
                        });
                }
                manifestPromise
                    .then(streamManifest => {
                        const channelData = streamManifest.find(stream => stream.id === streamId);
                        if (!channelData) throw new Error(`Channel dengan ID "${streamId}" tidak ditemukan.`);
                        return playDashStream(channelData.url, channelData.drm);
                    })
                    .catch(error => showError(error.message));
            }

            // --- Logika Utama ---
            const params = new URLSearchParams(window.location.search);
            const streamType = params.get('type');
            const streamValue = params.get('url');

            if (!streamType || !streamValue) {
                showError('Parameter "type" dan "url" wajib diisi.');
                return;
            }
            
            // --- LOGIKA IFRAME (YANG DITAMBAHKAN KEMBALI) ---
            if (streamType === 'iframe') {
                playIframeStream(streamValue);
            } else if (streamType === 'hls') {
                loadHlsStream(streamValue);
            } else if (streamType === 'dash') {
                playDashStream(streamValue, null); 
            } else if (streamType === 'tv') {
                playTvStream(streamValue);
            } else {
                showError('Tipe stream tidak didukung.');
            }
        });
    </script>
</body>
</html>
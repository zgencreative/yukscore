@extends('layouts.templates')

{{-- Mengatur judul halaman secara dinamis --}}
@section('title', $matchName ?? 'Live Stream')

{{-- Menambahkan CSS dan Style khusus untuk halaman ini --}}
@section('extra_head')
    {{-- CSS untuk Video.js (HLS) --}}
    <link href="https://vjs.zencdn.net/8.11.8/video-js.css" rel="stylesheet" />

    <style>
        .player-container {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .video-title {
            margin-bottom: 1.5rem;
            text-align: center;
            color: #fff;
        }
        .video-wrapper {
            position: relative;
            width: 100%;
            max-width: 900px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
            border-radius: 10px;
            overflow: hidden;
            border: 2px solid rgba(255, 255, 255, 0.1);
            background-color: #000;
            padding-top: 56.25%; /* Aspek rasio 16:9 */
        }
        /* Style untuk SEMUA player (video HLS, video DASH, dan iframe) agar responsif */
        #hls-player, #dash-player, #iframe-player {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: none;
        }
        .controls-group {
            width: 100%;
            max-width: 900px;
            margin-top: 1rem;
            padding: 10px;
            border: 1px solid #334155;
            border-radius: 8px;
            background-color: rgba(30, 41, 59, 0.5);
        }
        .controls-group h4 {
            margin: 0 0 10px 0;
            font-size: 1rem;
            color: #cbd5e1;
            font-weight: normal;
        }
        .server-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .server-btn {
            background-color: #334155;
            color: #fff;
            border: 1px solid #475569;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s, border-color 0.3s;
        }
        .server-btn:hover {
            background-color: #475569;
        }
        .server-btn.active {
            background-color: #1e40af;
            border-color: #2563eb;
            box-shadow: 0 0 10px rgba(37, 99, 235, 0.5);
        }
        .hidden {
            display: none !important;
        }
    </style>
@endsection


{{-- Konten utama halaman: Judul, Player, dan Tombol Server --}}
@section('content')
<div class="container my-4">
    <div class="player-container">

        {{-- Judul Pertandingan --}}
        <h1 class="video-title">🎥 {{ $matchName ?? 'Live Stream' }}</h1>

        {{-- Wrapper untuk Video Player --}}
        <div class="video-wrapper">
            <video id="hls-player" class="video-js vjs-default-skin"></video>
            <video id="dash-player" class="hidden"></video>
            <iframe id="iframe-player" class="hidden" allow="encrypted-media" allowfullscreen="true" scrolling="no"></iframe>
        </div>

        {{-- Grup Kontrol --}}
        @php
            $hlsAvailable = !empty($hlsStreams);
            $dashAvailable = !empty($dashStreams) || !empty($tv_links);
            $iframeAvailable = !empty($iframeStreams);
            $streamTypeCount = ($hlsAvailable ? 1 : 0) + ($dashAvailable ? 1 : 0) + ($iframeAvailable ? 1 : 0);
        @endphp

        {{-- 1. Tombol Tipe Player (HLS/DASH/iframe) --}}
        @if ($streamTypeCount > 1)
            <div class="controls-group" id="player-type-controls">
                <h4>Tipe Player</h4>
                <div class="server-buttons">
                    @if ($hlsAvailable)
                        <button class="server-btn" data-player-type="hls">Player HLS</button>
                    @endif
                    @if ($dashAvailable)
                        <button class="server-btn" data-player-type="dash">Player DASH</button>
                    @endif
                    @if ($iframeAvailable)
                        <button class="server-btn" data-player-type="iframe">Player iframe</button>
                    @endif
                </div>
            </div>
        @endif

        {{-- 2. Tombol Server HLS --}}
        @if ($hlsAvailable)
            <div class="controls-group" id="hls-server-controls">
                <h4>Server HLS</h4>
                <div class="server-buttons">
                    @foreach ($hlsStreams as $index => $streamUrl)
                        <button class="server-btn" data-src="{{ $streamUrl }}">Server {{ $index + 1 }}</button>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- 3. Tombol Server DASH (Kini termasuk tv_links) --}}
        @if ($dashAvailable)
            <div class="controls-group hidden" id="dash-server-controls">
                <h4>Server DASH</h4>
                <div class="server-buttons">
                    {{-- Tombol untuk dashStreams biasa --}}
                    @foreach ($dashStreams as $index => $streamUrl)
                        <button class="server-btn" data-src="{{ $streamUrl }}">Server {{ $index + 1 }}</button>
                    @endforeach
                    {{-- Tombol untuk tv_links (yang butuh pencarian) --}}
                    @foreach ($tv_links as $index => $tvId)
                        <button class="server-btn" data-tvlink-id="{{ $tvId }}">TV Link {{ $index + 1 }}</button>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- 4. Tombol Server iframe --}}
        @if ($iframeAvailable)
            <div class="controls-group hidden" id="iframe-server-controls">
                <h4>Server iframe</h4>
                <div class="server-buttons">
                    @foreach ($iframeStreams as $index => $streamUrl)
                        <button class="server-btn" data-src="{{ $streamUrl }}">Server {{ $index + 1 }}</button>
                    @endforeach
                </div>
            </div>
        @endif
        
    </div>
</div>
@endsection


{{-- Menambahkan JavaScript khusus untuk halaman ini --}}
@section('extra_js')
    {{-- Library Player --}}
    <script src="https://vjs.zencdn.net/8.11.8/video.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/shaka-player@4.7.13/dist/shaka-player.compiled.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', async function() {
            // --- Konfigurasi Awal ---
            const hlsStreams = @json($hlsStreams ?? []);
            const iframeStreams = @json($iframeStreams ?? []);
            const dashStreams = @json($dashStreams ?? []);
            const tvLinks = @json($tv_links ?? []);
            const proxyBaseUrl = @json($proxyBaseUrl ?? '');
            let streamManifestMap = {};

            // --- Seleksi Elemen DOM ---
            const hlsPlayerElement = document.getElementById('hls-player');
            const iframePlayerElement = document.getElementById('iframe-player');
            const dashPlayerElement = document.getElementById('dash-player');
            const playerTypeControls = document.getElementById('player-type-controls');
            const hlsServerControls = document.getElementById('hls-server-controls');
            const iframeServerControls = document.getElementById('iframe-server-controls');
            const dashServerControls = document.getElementById('dash-server-controls');

            // --- Fungsi Fetch Data ---
            async function fetchStreamManifest() {
                try {
                    // <<< PERUBAHAN: Menggunakan proxy lokal untuk menghindari CORS
                    const response = await fetch('/proxy/streams');
                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                    const data = await response.json();
                    data.forEach(item => { streamManifestMap[item.id] = item; });
                    console.log("Manifest stream berhasil dimuat via proxy.");
                } catch (error) {
                    console.error("Gagal memuat manifest stream:", error);
                    document.querySelectorAll('[data-tvlink-id]').forEach(btn => {
                        btn.disabled = true;
                        btn.style.backgroundColor = '#555';
                        btn.textContent += ' (Error)';
                    });
                }
            }

            await fetchStreamManifest();

            // --- Cek Ketersediaan & Inisialisasi Player ---
            const isAnyStreamAvailable = hlsStreams.length > 0 || iframeStreams.length > 0 || dashStreams.length > 0 || tvLinks.length > 0;
            if (!isAnyStreamAvailable) {
                document.querySelector('.video-wrapper').innerHTML = '<p style="text-align:center; padding: 40px; color: #ffcccc;">Stream tidak ditemukan.</p>';
                return;
            }

            let hlsPlayer, dashPlayerInstance;
            if (hlsStreams.length > 0) {
                hlsPlayer = videojs(hlsPlayerElement, { autoplay: true, controls: true, responsive: true });
            }
            if (dashStreams.length > 0 || tvLinks.length > 0) {
                dashPlayerInstance = new shaka.Player(dashPlayerElement);
                dashPlayerElement.autoplay = true;
                dashPlayerElement.controls = true;
                dashPlayerInstance.addEventListener('error', e => console.error('Shaka Player Error:', e.detail));
            }

            // --- FUNGSI-FUNGSI UTAMA ---
            function loadHlsStream(url) {
                if (!hlsPlayer) return;
                const proxiedUrl = url.includes('su.carryflix.workers.dev') ? `${proxyBaseUrl}/hls?url=${encodeURIComponent(url)}` : url;
                hlsPlayer.src({ src: proxiedUrl, type: 'application/x-mpegURL' });
            }

            function loadDashStream(url, drmData = null) {
                if (!dashPlayerInstance) return;

                console.log("Memuat DASH Stream:", url);
                let drmConfiguration = {}; // Default: tanpa DRM

                if (drmData) {
                    if (drmData.widevine) {
                        console.log("Mengonfigurasi DRM Widevine");
                        drmConfiguration = {
                            servers: { 'com.widevine.alpha': drmData.widevine }
                        };
                    } 
                    else if (drmData.clearkeys || drmData.clearkey) {
                        console.log("Mengonfigurasi DRM ClearKey");
                        drmConfiguration = {
                            clearKeys: drmData.clearkeys || drmData.clearkey
                        };
                    }
                }
                
                console.log("Konfigurasi Shaka Player:", { drm: drmConfiguration });
                dashPlayerInstance.configure({ drm: drmConfiguration });
                dashPlayerInstance.load(url).catch(e => console.error('Error loading DASH stream:', e));
            }

            function loadIframeStream(url) {
                iframePlayerElement.src = url;
            }

            function switchPlayerView(playerType) {
                const playerMap = {
                    hls: { player: hlsPlayerElement, controls: hlsServerControls },
                    dash: { player: dashPlayerElement, controls: dashServerControls },
                    iframe: { player: iframePlayerElement, controls: iframeServerControls }
                };
                Object.values(playerMap).forEach(({ player, controls }) => {
                    player?.classList.add('hidden');
                    controls?.classList.add('hidden');
                });
                if (hlsPlayer) hlsPlayer.pause();
                if (dashPlayerInstance?.getMediaElement()) dashPlayerInstance.getMediaElement().pause();
                iframePlayerElement.src = 'about:blank';
                const selected = playerMap[playerType];
                if (selected) {
                    selected.player?.classList.remove('hidden');
                    selected.controls?.classList.remove('hidden');
                }
            }
            
            // --- EVENT LISTENERS ---
            function setupButtonActivation(controlsContainer, clickedButton) {
                 if (!controlsContainer) return;
                 controlsContainer.querySelectorAll('.server-btn').forEach(btn => btn.classList.remove('active'));
                 clickedButton?.classList.add('active');
            }

            if (hlsServerControls) {
                hlsServerControls.addEventListener('click', (e) => {
                    const button = e.target.closest('button[data-src]');
                    if (!button) return;
                    loadHlsStream(button.dataset.src);
                    setupButtonActivation(hlsServerControls, button);
                });
            }

            if (iframeServerControls) {
                iframeServerControls.addEventListener('click', (e) => {
                    const button = e.target.closest('button[data-src]');
                    if (!button) return;
                    loadIframeStream(button.dataset.src);
                    setupButtonActivation(iframeServerControls, button);
                });
            }

            if (dashServerControls) {
                dashServerControls.addEventListener('click', (e) => {
                    const button = e.target.closest('button');
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
                playerTypeControls.addEventListener('click', (e) => {
                    const button = e.target.closest('button[data-player-type]');
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
                switchPlayerView('hls');
                playerTypeControls?.querySelector('[data-player-type="hls"]')?.click();
                hlsServerControls.querySelector('.server-btn')?.click();
            } else if (dashAvailable) {
                switchPlayerView('dash');
                playerTypeControls?.querySelector('[data-player-type="dash"]')?.click();
                dashServerControls.querySelector('.server-btn')?.click();
            } else if (iframeAvailable) {
                switchPlayerView('iframe');
                playerTypeControls?.querySelector('[data-player-type="iframe"]')?.click();
                iframeServerControls.querySelector('.server-btn')?.click();
            }
        });
    </script>
@endsection

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

        {{-- Wrapper untuk Video Player, kini berisi HLS, DASH, dan iframe --}}
        <div class="video-wrapper">
            {{-- Player HLS (Video.js) --}}
            <video id="hls-player" class="video-js vjs-default-skin"></video>

            {{-- Player DASH (Shaka Player) --}}
            <video id="dash-player" class="hidden"></video>

            {{-- Player Iframe --}}
            <iframe id="iframe-player" class="hidden" allow="encrypted-media" allowfullscreen="true" scrolling="no"></iframe>
        </div>

        {{-- Grup Kontrol --}}
        {{-- 1. Tombol Tipe Player (HLS/DASH/iframe) --}}
        @php
            // Hitung jumlah tipe stream yang tersedia
            $streamTypeCount = (empty($hlsStreams) ? 0 : 1) + (empty($dashStreams) ? 0 : 1) + (empty($iframeStreams) ? 0 : 1);
        @endphp

        @if ($streamTypeCount > 1)
            <div class="controls-group" id="player-type-controls">
                <h4>Tipe Player</h4>
                <div class="server-buttons">
                    @if (!empty($hlsStreams))
                        <button class="server-btn" data-player-type="hls">Player HLS</button>
                    @endif
                    @if (!empty($dashStreams))
                        <button class="server-btn" data-player-type="dash">Player DASH</button>
                    @endif
                    @if (!empty($iframeStreams))
                        <button class="server-btn" data-player-type="iframe">Player iframe</button>
                    @endif
                </div>
            </div>
        @endif

        {{-- 2. Tombol Server HLS --}}
        @if (!empty($hlsStreams))
            <div class="controls-group" id="hls-server-controls">
                <h4>Server HLS</h4>
                <div class="server-buttons">
                    @foreach ($hlsStreams as $index => $streamUrl)
                        <button class="server-btn" data-src="{{ $streamUrl }}">Server {{ $index + 1 }}</button>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- 3. Tombol Server DASH (BARU) --}}
        @if (!empty($dashStreams))
            <div class="controls-group hidden" id="dash-server-controls">
                <h4>Server DASH</h4>
                <div class="server-buttons">
                    @foreach ($dashStreams as $index => $streamUrl)
                        <button class="server-btn" data-src="{{ $streamUrl }}">Server {{ $index + 1 }}</button>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- 4. Tombol Server iframe --}}
        @if (!empty($iframeStreams))
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
    {{-- Library untuk HLS Player --}}
    <script src="https://vjs.zencdn.net/8.11.8/video.min.js"></script>
    {{-- Library untuk DASH Player (BARU) --}}
    <script src="https://cdn.jsdelivr.net/npm/shaka-player@4.7.13/dist/shaka-player.compiled.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- Konfigurasi Awal ---
            const hlsStreams = @json($hlsStreams ?? []);
            const iframeStreams = @json($iframeStreams ?? []);
            const dashStreams = @json($dashStreams ?? []); // Ambil data dash
            const proxyBaseUrl = @json($proxyBaseUrl ?? '');

            // --- Seleksi Elemen DOM ---
            const hlsPlayerElement = document.getElementById('hls-player');
            const iframePlayerElement = document.getElementById('iframe-player');
            const dashPlayerElement = document.getElementById('dash-player'); // Player baru
            
            const playerTypeControls = document.getElementById('player-type-controls');
            const hlsServerControls = document.getElementById('hls-server-controls');
            const iframeServerControls = document.getElementById('iframe-server-controls');
            const dashServerControls = document.getElementById('dash-server-controls'); // Kontrol baru

            // Cek apakah ada stream yang tersedia
            if (hlsStreams.length === 0 && iframeStreams.length === 0 && dashStreams.length === 0) {
                document.querySelector('.video-wrapper').innerHTML = '<p style="text-align:center; padding: 40px; color: #ffcccc;">Stream tidak ditemukan untuk pertandingan ini.</p>';
                return;
            }

            // --- Inisialisasi Player ---
            let hlsPlayer, dashPlayerInstance;

            // Inisialisasi Video.js Player hanya jika ada stream HLS
            if (hlsStreams.length > 0) {
                hlsPlayer = videojs(hlsPlayerElement, { autoplay: true, controls: true, responsive: true });
            } else {
                hlsPlayerElement.style.display = 'none';
            }

            // Inisialisasi Shaka Player hanya jika ada stream DASH
            if (dashStreams.length > 0) {
                dashPlayerInstance = new shaka.Player(dashPlayerElement);
                // Konfigurasi dasar Shaka Player
                dashPlayerElement.autoplay = true;
                dashPlayerElement.controls = true;
                dashPlayerInstance.addEventListener('error', e => console.error('Shaka Player Error:', e.detail));
            } else {
                dashPlayerElement.style.display = 'none';
            }


            // --- FUNGSI-FUNGSI UTAMA ---
            function loadHlsStream(url) {
                if (!hlsPlayer) return;
                const proxiedUrl = url.includes('su.carryflix.workers.dev')
                ? `${proxyBaseUrl}/hls?url=${encodeURIComponent(url)}`
                : url;
                console.log("Memuat HLS Stream:", proxiedUrl);
                hlsPlayer.src({ src: proxiedUrl, type: 'application/x-mpegURL' });
            }

            function loadDashStream(url) {
                if (!dashPlayerInstance) return;
                console.log("Memuat DASH Stream:", url);
                dashPlayerInstance.load(url).catch(e => console.error('Error loading DASH stream:', e));
            }

            function loadIframeStream(url) {
                console.log("Memuat iframe Stream:", url);
                iframePlayerElement.src = url;
            }

            function switchPlayerView(playerType) {
                const elements = [hlsPlayerElement, dashPlayerElement, iframePlayerElement];
                const controls = [hlsServerControls, dashServerControls, iframeServerControls];

                // Sembunyikan semua terlebih dahulu
                elements.forEach(el => el.classList.add('hidden'));
                controls.forEach(ctrl => ctrl?.classList.add('hidden')); // `?.` untuk safety

                if (playerType === 'hls') {
                    hlsPlayerElement.classList.remove('hidden');
                    hlsServerControls?.classList.remove('hidden');
                } else if (playerType === 'dash') {
                    dashPlayerElement.classList.remove('hidden');
                    dashServerControls?.classList.remove('hidden');
                } else if (playerType === 'iframe') {
                    iframePlayerElement.classList.remove('hidden');
                    iframeServerControls?.classList.remove('hidden');
                }
            }


            // --- EVENT LISTENERS ---
            function setupEventListeners(controls, callback) {
                if (!controls) return;
                controls.addEventListener('click', (e) => {
                    const button = e.target.closest('button');
                    if (!button || !button.dataset.src) return;
                    
                    callback(button.dataset.src);
                    
                    controls.querySelectorAll('.server-btn').forEach(btn => btn.classList.remove('active'));
                    button.classList.add('active');
                });
            }

            setupEventListeners(hlsServerControls, loadHlsStream);
            setupEventListeners(dashServerControls, loadDashStream);
            setupEventListeners(iframeServerControls, loadIframeStream);

            // Listener untuk tombol Tipe Player
            if (playerTypeControls) {
                playerTypeControls.addEventListener('click', (e) => {
                    const button = e.target.closest('button');
                    if (!button || !button.dataset.playerType) return;

                    const playerType = button.dataset.playerType;
                    switchPlayerView(playerType);

                    playerTypeControls.querySelectorAll('.server-btn').forEach(btn => btn.classList.remove('active'));
                    button.classList.add('active');
                });
            }


            // --- LOGIKA INISIALISASI ---
            // Tentukan player mana yang akan ditampilkan pertama kali (Prioritas: HLS > DASH > iframe)
            if (hlsStreams.length > 0) {
                switchPlayerView('hls');
                playerTypeControls?.querySelector('[data-player-type="hls"]')?.classList.add('active');
                hlsServerControls.querySelector('.server-btn')?.click();
            } else if (dashStreams.length > 0) {
                switchPlayerView('dash');
                playerTypeControls?.querySelector('[data-player-type="dash"]')?.classList.add('active');
                dashServerControls.querySelector('.server-btn')?.click();
            } else if (iframeStreams.length > 0) {
                switchPlayerView('iframe');
                playerTypeControls?.querySelector('[data-player-type="iframe"]')?.classList.add('active');
                iframeServerControls.querySelector('.server-btn')?.click();
            }
        });
    </script>
@endsection
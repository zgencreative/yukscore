@extends('layouts.templates')

{{-- Mengatur judul halaman secara dinamis --}}
@section('title', $matchName ?? 'Live Stream')

{{-- Menambahkan CSS dan Style khusus untuk halaman ini --}}
@section('extra_head')
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
            color: #fff; /* Pastikan warna sesuai dengan template Anda */
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
            /* Memberi aspek rasio 16:9 pada wrapper */
            padding-top: 56.25%;
        }
        /* Style untuk kedua player (video dan iframe) agar responsif */
        .video-js, .video-wrapper iframe {
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

        {{-- Wrapper untuk Video Player, kini berisi HLS dan iframe --}}
        <div class="video-wrapper">
            <video id="hls-player" class="video-js vjs-default-skin"></video>
            <iframe id="iframe-player" class="hidden" allow="encrypted-media" allowfullscreen="true" scrolling="no"></iframe>
        </div>

        {{-- Grup Kontrol --}}
        {{-- 1. Tombol Tipe Player (HLS/iframe) --}}
        @if (!empty($hlsStreams) && !empty($iframeStreams))
            <div class="controls-group" id="player-type-controls">
                <h4>Tipe Player</h4>
                <div class="server-buttons">
                    <button class="server-btn" data-player-type="hls">Player HLS</button>
                    <button class="server-btn" data-player-type="iframe">Player iframe</button>
                </div>
            </div>
        @endif

        {{-- 2. Tombol Server HLS --}}
        @if (!empty($hlsStreams))
            <div class="controls-group" id="hls-server-controls">
                <h4>Server HLS</h4>
                <div class="server-buttons">
                    @foreach ($hlsStreams as $index => $streamUrl)
                        <button class="server-btn" data-src="{{ $streamUrl }}">
                            Server {{ $index + 1 }}
                        </button>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- 3. Tombol Server iframe --}}
        @if (!empty($iframeStreams))
            <div class="controls-group hidden" id="iframe-server-controls">
                <h4>Server iframe</h4>
                <div class="server-buttons">
                    @foreach ($iframeStreams as $index => $streamUrl)
                        <button class="server-btn" data-src="{{ $streamUrl }}">
                            Server {{ $index + 1 }}
                        </button>
                    @endforeach
                </div>
            </div>
        @endif
        
    </div>
</div>
@endsection


{{-- Menambahkan JavaScript khusus untuk halaman ini --}}
@section('extra_js')
    <script src="https://vjs.zencdn.net/8.11.8/video.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- Konfigurasi Awal ---
            const hlsStreams = @json($hlsStreams ?? []);
            const iframeStreams = @json($iframeStreams ?? []);
            const proxyBaseUrl = @json($proxyBaseUrl ?? '');

            // --- Seleksi Elemen DOM ---
            const videoPlayer = document.getElementById('hls-player');
            const iframePlayer = document.getElementById('iframe-player');
            const playerTypeControls = document.getElementById('player-type-controls');
            const hlsServerControls = document.getElementById('hls-server-controls');
            const iframeServerControls = document.getElementById('iframe-server-controls');

            // Cek apakah ada stream yang tersedia
            if (hlsStreams.length === 0 && iframeStreams.length === 0) {
                document.querySelector('.video-wrapper').innerHTML = '<p style="text-align:center; padding: 40px; color: #ffcccc;">Stream tidak ditemukan untuk pertandingan ini.</p>';
                return;
            }

            // Inisialisasi Video.js Player hanya jika ada stream HLS
            let player;
            if (hlsStreams.length > 0) {
                player = videojs(videoPlayer, {
                    autoplay: true,
                    controls: true,
                    responsive: true,
                });
            } else {
                // Jika tidak ada HLS, sembunyikan player video
                videoPlayer.style.display = 'none';
            }


            // --- FUNGSI-FUNGSI UTAMA ---

            // Fungsi untuk memuat stream HLS ke Video.js
            function loadHlsStream(url) {
                if (!player) return;
                const proxiedUrl = `${proxyBaseUrl}/hls?url=${encodeURIComponent(url)}`;
                console.log("Memuat HLS Stream:", proxiedUrl);
                player.src({ src: proxiedUrl, type: 'application/x-mpegURL' });
            }

            // Fungsi untuk memuat URL ke dalam iframe
            function loadIframeStream(url) {
                console.log("Memuat iframe Stream:", url);
                iframePlayer.src = url;
            }

            // Fungsi untuk mengganti tampilan player
            function switchPlayerView(playerType) {
                if (playerType === 'hls') {
                    videoPlayer.classList.remove('hidden');
                    iframePlayer.classList.add('hidden');
                    hlsServerControls.classList.remove('hidden');
                    iframeServerControls.classList.add('hidden');
                } else if (playerType === 'iframe') {
                    videoPlayer.classList.add('hidden');
                    iframePlayer.classList.remove('hidden');
                    hlsServerControls.classList.add('hidden');
                    iframeServerControls.classList.remove('hidden');
                }
            }


            // --- EVENT LISTENERS ---

            // Listener untuk tombol Tipe Player
            if (playerTypeControls) {
                playerTypeControls.addEventListener('click', (e) => {
                    const button = e.target.closest('button');
                    if (!button) return;

                    const playerType = button.dataset.playerType;
                    switchPlayerView(playerType);

                    // Update 'active' state
                    playerTypeControls.querySelectorAll('.server-btn').forEach(btn => btn.classList.remove('active'));
                    button.classList.add('active');
                });
            }

            // Listener untuk tombol Server HLS
            if (hlsServerControls) {
                hlsServerControls.addEventListener('click', (e) => {
                    const button = e.target.closest('button');
                    if (!button) return;

                    loadHlsStream(button.dataset.src);
                    
                    // Update 'active' state
                    hlsServerControls.querySelectorAll('.server-btn').forEach(btn => btn.classList.remove('active'));
                    button.classList.add('active');
                });
            }

            // Listener untuk tombol Server iframe
            if (iframeServerControls) {
                iframeServerControls.addEventListener('click', (e) => {
                    const button = e.target.closest('button');
                    if (!button) return;

                    loadIframeStream(button.dataset.src);
                    
                    // Update 'active' state
                    iframeServerControls.querySelectorAll('.server-btn').forEach(btn => btn.classList.remove('active'));
                    button.classList.add('active');
                });
            }


            // --- LOGIKA INISIALISASI ---

            if (hlsStreams.length > 0) {
                // Prioritaskan HLS jika tersedia
                // Tandai tombol player HLS sebagai aktif
                playerTypeControls?.querySelector('[data-player-type="hls"]').classList.add('active');
                // Klik tombol server HLS pertama secara otomatis
                hlsServerControls.querySelector('.server-btn').click();
            } 
            else if (iframeStreams.length > 0) {
                // Jika hanya iframe yang tersedia
                switchPlayerView('iframe');
                // Tandai tombol player iframe sebagai aktif
                playerTypeControls?.querySelector('[data-player-type="iframe"]').classList.add('active');
                 // Klik tombol server iframe pertama secara otomatis
                iframeServerControls.querySelector('.server-btn').click();
            }
        });
    </script>
@endsection
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
            width: 100%;
            max-width: 900px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
            border-radius: 10px;
            overflow: hidden;
            border: 2px solid rgba(255, 255, 255, 0.1);
            background-color: #000;
        }
        .video-js {
            width: 100%;
            height: 500px;
        }
        .server-buttons {
            margin-top: 1rem;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: center;
        }
        .server-btn {
            background-color: #334155;
            color: #fff;
            border: 1px solid #475569;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        .server-btn:hover {
            background-color: #475569;
        }
        .server-btn.active {
            background-color: #1e40af;
            border-color: #2563eb;
        }
        @media (max-width: 768px) {
            .video-js {
                height: auto;
                min-height: 280px;
            }
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
            <video id="hls-player" class="video-js vjs-default-skin" controls preload="auto"></video>
        </div>

        {{-- Tombol Pilihan Server (hanya muncul jika ada lebih dari 1 stream) --}}
        @if (!empty($hlsStreams) && count($hlsStreams) > 1)
            <div class="server-buttons">
                @foreach ($hlsStreams as $index => $streamUrl)
                    <button class="server-btn {{ $loop->first ? 'active' : '' }}" onclick="changeStream(this, '{{ $streamUrl }}')">
                        Server {{ $index + 1 }}
                    </button>
                @endforeach
            </div>
        @endif
        
    </div>
</div>
@endsection


{{-- Menambahkan JavaScript khusus untuk halaman ini --}}
@section('extra_js')
    <script src="https://vjs.zencdn.net/8.11.8/video.min.js"></script>

    <script>
        let player;

        // Jadikan `changeStream` fungsi global agar bisa diakses oleh atribut onclick
        window.changeStream = function(buttonElement, originalUrl) {
            if (player) {
                const proxyBaseUrl = window.myApp.proxyUrl;

                if (!proxyBaseUrl) {
                    console.error("Proxy URL tidak terdefinisi.");
                    return;
                }

                const proxiedUrl = `${proxyBaseUrl}/hls?url=${encodeURIComponent(originalUrl)}`;
                console.log("Mengganti stream ke (via proxy):", proxiedUrl);
                
                player.src({
                    src: proxiedUrl,
                    type: 'application/x-mpegURL'
                });

                document.querySelectorAll('.server-btn').forEach(btn => btn.classList.remove('active'));
                buttonElement.classList.add('active');
            }
        }

        // Jalankan kode setelah seluruh halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            try {
                // Ambil data dari Controller Laravel
                const originalStreamUrls = @json($hlsStreams ?? []);
                // Simpan proxy URL di objek window agar bisa diakses oleh changeStream
                window.myApp = {
                    proxyUrl: @json($proxyBaseUrl ?? '')
                };
                
                if (originalStreamUrls.length === 0 || !window.myApp.proxyUrl) {
                    console.error("Data stream atau proxy URL tidak tersedia untuk memulai player.");
                    const videoContainer = document.querySelector('.video-wrapper');
                    if(videoContainer) {
                        videoContainer.innerHTML = '<p style="text-align:center; padding: 40px; color: #ffcccc;">Stream tidak dapat dimuat.</p>';
                    }
                    return;
                }

                const initialProxiedUrl = `${window.myApp.proxyUrl}/hls?url=${encodeURIComponent(originalStreamUrls[0])}`;

                const playerOptions = {
                    autoplay: true,
                    controls: true,
                    responsive: true,
                    fluid: true,
                    sources: [{
                        src: initialProxiedUrl,
                        type: 'application/x-mpegURL'
                    }]
                };

                // Inisialisasi player
                player = videojs('hls-player', playerOptions);

            } catch (e) {
                console.error("Terjadi error saat inisialisasi player:", e);
            }
        });
    </script>
@endsection
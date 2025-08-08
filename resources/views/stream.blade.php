@extends('layouts.templates')

{{-- Mengatur judul halaman secara dinamis --}}
@section('title', $matchName ?? 'Live Stream')

{{-- Menambahkan CSS dan Style khusus untuk halaman ini --}}
@section('extra_head')
    {{-- CSS untuk Video.js (HLS) --}}
    <link href="https://vjs.zencdn.net/8.11.8/video-js.css" rel="stylesheet" />

    <style>
        /* == STRUKTUR LAYOUT BARU == */
        .stream-layout {
            display: flex;
            gap: 20px; /* Jarak antara video dan chat */
            align-items: flex-start; /* Konten align di atas */
        }
        .video-column {
            flex: 3; /* Kolom video lebih lebar */
            min-width: 0;
        }
        .chat-column {
            flex: 1; /* Kolom chat lebih sempit */
            background-color: #1e293b; /* Warna latar gelap */
            border-radius: 10px;
            border: 1px solid #334155;
            height: 50vw; /* Tinggi chat box (sesuaikan jika perlu) */
            max-height: 700px;
            display: flex;
            flex-direction: column;
        }
        
        /* == STYLE UNTUK LIVE CHAT == */
        .chat-column h3 {
            padding: 15px;
            margin: 0;
            border-bottom: 1px solid #334155;
            font-size: 1.1rem;
            color: #e2e8f0;
            text-align: center;
        }
        .chat-messages {
            flex-grow: 1;
            padding: 15px;
            overflow-y: auto; /* Agar bisa di-scroll */
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .chat-message {
            display: flex;
            flex-direction: column;
            line-height: 1.4;
        }
        .chat-message .username {
            font-weight: bold;
            color: #60a5fa; /* Warna username */
            font-size: 0.9em;
            margin-bottom: 2px;
        }
        .chat-message .text {
            color: #cbd5e1; /* Warna teks pesan */
            font-size: 0.95em;
            word-wrap: break-word;
        }
        .chat-form {
            display: flex;
            padding: 15px;
            border-top: 1px solid #334155;
            gap: 10px;
        }
        .chat-form input {
            flex-grow: 1;
            background-color: #334155;
            border: 1px solid #475569;
            color: #fff;
            padding: 10px;
            border-radius: 5px;
            outline: none;
        }
        .chat-form button {
            background-color: #2563eb;
            color: #fff;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .chat-form button:hover {
            background-color: #1d4ed8;
        }

        /* == Responsif untuk layar kecil == */
        @media (max-width: 1024px) {
            .stream-layout {
                flex-direction: column; /* Tumpuk video dan chat secara vertikal */
            }
            .video-column, .chat-column {
                width: 100%;
                flex: none;
            }
            .chat-column {
                height: 60vh; /* Sesuaikan tinggi chat di mode mobile */
            }
        }

        /* == STYLE PLAYER LAMA == */
        .video-title {
            margin-bottom: 1.5rem; text-align: center; color: #fff;
        }
        .video-wrapper {
            position: relative; width: 100%; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
            border-radius: 10px; overflow: hidden; border: 2px solid rgba(255, 255, 255, 0.1);
            background-color: #000; padding-top: 56.25%;
        }
        #hls-player, #dash-player, #iframe-player {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: none;
        }
        .controls-group {
            width: 100%; margin-top: 1rem; padding: 10px; border: 1px solid #334155;
            border-radius: 8px; background-color: rgba(30, 41, 59, 0.5);
        }
        .controls-group h4 {
            margin: 0 0 10px 0; font-size: 1rem; color: #cbd5e1; font-weight: normal;
        }
        .server-buttons {
            display: flex; gap: 10px; flex-wrap: wrap;
        }
        .server-btn {
            background-color: #334155; color: #fff; border: 1px solid #475569;
            padding: 8px 16px; border-radius: 5px; cursor: pointer;
            font-weight: bold; transition: background-color 0.3s, border-color 0.3s;
        }
        .server-btn:hover { background-color: #475569; }
        .server-btn.active {
            background-color: #1e40af; border-color: #2563eb; box-shadow: 0 0 10px rgba(37, 99, 235, 0.5);
        }
        .hidden { display: none !important; }
        .controls-disabled {
            opacity: 0.5;
            pointer-events: none; /* Ini akan menonaktifkan semua klik mouse */
        }
    </style>
@endsection


{{-- Konten utama halaman: Judul, Player, dan Tombol Server --}}
@section('content')
<div class="container my-4">
    <div id="stream-data"
        data-hls-streams="{{ json_encode($hlsStreams ?? []) }}"
        data-iframe-streams="{{ json_encode($iframeStreams ?? []) }}"
        data-dash-streams="{{ json_encode($dashStreams ?? []) }}"
        data-tv-links="{{ json_encode($tv_links ?? []) }}"
        data-proxy-base-url="{{ json_encode($proxyBaseUrl ?? '') }}"
        style="display: none;">
    </div>
    <div class="stream-layout">

        {{-- KOLOM KIRI: VIDEO PLAYER & KONTROL --}}
        <div class="video-column">
            <h1 class="video-title">🎥 {{ $matchName ?? 'Live Stream' }}</h1>

            <div class="video-wrapper">
                <video id="hls-player" class="video-js vjs-default-skin"></video>
                <video id="dash-player" class="hidden"></video>
                <iframe id="iframe-player" class="hidden" allow="encrypted-media" allowfullscreen="true" scrolling="no"></iframe>
            </div>

            @php
                $hlsAvailable = !empty($hlsStreams);
                $dashAvailable = !empty($dashStreams) || !empty($tv_links);
                $iframeAvailable = !empty($iframeStreams);
                $streamTypeCount = ($hlsAvailable ? 1 : 0) + ($dashAvailable ? 1 : 0) + ($iframeAvailable ? 1 : 0);
            @endphp
            @if ($streamTypeCount > 1)
                <div class="controls-group" id="player-type-controls">
                    <h4>Tipe Player</h4>
                    <div class="server-buttons">
                        @if ($hlsAvailable)<button class="server-btn" data-player-type="hls">Player HLS</button>@endif
                        @if ($dashAvailable)<button class="server-btn" data-player-type="dash">Player DASH</button>@endif
                        @if ($iframeAvailable)<button class="server-btn" data-player-type="iframe">Player iframe</button>@endif
                    </div>
                </div>
            @endif
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
            @if ($dashAvailable)
                <div class="controls-group hidden" id="dash-server-controls">
                    <h4>Server DASH</h4>
                    <div class="server-buttons">
                        @foreach ($dashStreams as $index => $streamUrl)
                            <button class="server-btn" data-src="{{ $streamUrl }}">Server {{ $index + 1 }}</button>
                        @endforeach
                        @foreach ($tv_links as $index => $tvId)
                            <button class="server-btn" data-tvlink-id="{{ $tvId }}">TV Link {{ $index + 1 }}</button>
                        @endforeach
                    </div>
                </div>
            @endif
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

        {{-- KOLOM KANAN: LIVE CHAT --}}
        <div class="chat-column" id="chat-app"
            data-match-id="{{ $matchId }}"
            data-initial-messages='@json($chatMessages)'
            data-user='@json($user)'
        >
        </div>

    </div>
</div>
@endsection


{{-- Menambahkan JavaScript khusus untuk halaman ini --}}
@section('extra_js')
    {{-- Library Player --}}
    <script src="https://vjs.zencdn.net/8.11.8/video.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/shaka-player@4.7.13/dist/shaka-player.compiled.js"></script>
    <script>
        function showSignupRedirectAlert() {
            Swal.fire({
                title: "Informasi Pendaftaran",
                text: "Silahkan Daftar Melalui Yuksports. Anda akan diarahkan dalam 5 detik.",
                icon: "info",
                timer: 5000, // Waktu dalam milidetik (5000ms = 5 detik)
                timerProgressBar: true, // Menampilkan bar timer
                allowOutsideClick: false, // Mencegah pengguna menutup alert
                didOpen: () => {
                    Swal.showLoading(); // Menampilkan ikon loading
                },
                willClose: () => {
                    // Ganti dengan URL pendaftaran Yuksports yang sebenarnya
                    window.location.href = "https://yuksports.com/";
                },
            });
        }

        window.showSignupRedirectAlert = showSignupRedirectAlert;
    </script>
@endsection

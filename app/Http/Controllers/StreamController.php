<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StreamController extends Controller
{
    public function playById(Request $request, $matchId)
    {
        // Ambil base URL API dari file konfigurasi .env
        $proxyBaseUrl = config('services.stream_proxy.base_url');

        if (!$proxyBaseUrl) {
            abort(500, 'Konfigurasi URL server stream tidak ditemukan.');
        }
        
        $scheduleApiUrl = "{$proxyBaseUrl}/schedule";

        try {
            // Lakukan request ke API schedule dengan timeout
            $response = Http::timeout(15)->get($scheduleApiUrl);

            // Jika request gagal, tampilkan error
            if ($response->failed()) {
                abort(502, 'Tidak dapat terhubung ke server jadwal.');
            }

            $scheduleData = $response->json();
            
            // Gunakan Laravel Collection untuk mencari data dengan mudah dan efisien
            $event = collect($scheduleData)->firstWhere('id_match', $matchId);

            // --- PERUBAHAN LOGIKA PENGECEKAN ---
            // Abort jika event tidak ditemukan, ATAU jika SEMUA jenis stream kosong.
            if (!$event || (empty($event['hls_streams']) && empty($event['iframe_streams']) && empty($event['dash_streams']))) {
                abort(404, 'Stream untuk pertandingan ini tidak ditemukan atau tidak tersedia.');
            }

            // --- PERUBAHAN PENGAMBILAN DATA ---
            // Ambil semua jenis stream. Gunakan '?? []' sebagai fallback jika key tidak ada.
            $hlsStreams = $event['hls_streams'] ?? [];
            $iframeStreams = $event['iframe_streams'] ?? [];
            $dashStreams = $event['dash_streams'] ?? []; // Tambahkan ini
            $matchName = $event['match_name'] ?? 'Detail Pertandingan';
            
            // --- PERUBAHAN DATA YANG DIKIRIM KE VIEW ---
            // Kirim semua data yang relevan ke view
            return view('stream', [
                'hlsStreams' => $hlsStreams,
                'iframeStreams' => $iframeStreams,
                'dashStreams' => $dashStreams, // Tambahkan ini
                'matchName' => $matchName,
                'proxyBaseUrl' => $proxyBaseUrl
            ]);

        } catch (\Exception $e) {
            Log::error("Error di StreamController: " . $e->getMessage());
            abort(500, 'Terjadi kesalahan pada server.');
        }
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http; // Gunakan HTTP Client
use Illuminate\Support\Facades\Log;

class StreamController extends Controller
{
    /**
     * Menampilkan halaman pemutar video berdasarkan ID Pertandingan.
     * Data diambil secara real-time dari API schedule.
     */
    public function playById(Request $request, $matchId)
    {
        // Ambil base URL API dari file konfigurasi .env
        $proxyBaseUrl = config('services.stream_proxy.base_url');

        if (!$proxyBaseUrl) {
            abort(500, 'Konfigurasi URL server stream tidak ditemukan.');
        }
        
        $scheduleApiUrl = "{$proxyBaseUrl}/schedule";

        try {
            // Lakukan request ke API schedule
            $response = Http::get($scheduleApiUrl);

            // Jika request gagal, tampilkan error
            if ($response->failed()) {
                abort(502, 'Tidak dapat terhubung ke server jadwal.');
            }

            $scheduleData = $response->json();
            
            // Gunakan Laravel Collection untuk mencari data dengan mudah dan efisien
            $event = collect($scheduleData)->firstWhere('id_match', $matchId);

            // Jika event tidak ditemukan atau tidak punya stream, tampilkan error
            if (!$event || empty($event['hls_streams'])) {
                abort(404, 'Stream untuk pertandingan ini tidak ditemukan atau tidak tersedia.');
            }

            // Ambil array hls_streams dan nama pertandingan
            $hlsStreams = $event['hls_streams'];
            $matchName = $event['match_name'];
            
            // Kirim array stream dan nama pertandingan ke view
            return view('stream', [
                'hlsStreams' => $hlsStreams,
                'matchName' => $matchName,
                'proxyBaseUrl' => $proxyBaseUrl
            ]);

        } catch (\Exception $e) {
            Log::error("Error di StreamController: " . $e->getMessage());
            abort(500, 'Terjadi kesalahan pada server.');
        }
    }
}
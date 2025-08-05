<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\ChatMessage;
use Illuminate\Support\Facades\Auth; 

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

            // Abort jika event tidak ditemukan, ATAU jika SEMUA jenis stream kosong.
            if (!$event || (empty($event['hls_streams']) && empty($event['iframe_streams']) && empty($event['dash_streams']) && empty($event['tv_links']))) {
                abort(404, 'Stream untuk pertandingan ini tidak ditemukan atau tidak tersedia.');
            }

            // Ambil semua jenis stream. Gunakan '?? []' sebagai fallback jika key tidak ada.
            $hlsStreams = $event['hls_streams'] ?? [];
            $iframeStreams = $event['iframe_streams'] ?? [];
            $dashStreams = $event['dash_streams'] ?? [];
            $tvLinks = $event['tv_links'] ?? [];
            $matchName = $event['match_name'] ?? 'Detail Pertandingan';

            $chatMessages = ChatMessage::where('match_id', $matchId)
                            ->latest()
                            ->take(50)
                            ->get()
                            ->reverse()
                            ->values() // <-- Tambahkan ini untuk mereset key
                            ->all(); 
            
            // Kirim semua data yang relevan ke view
            return view('stream', [
                'matchId' => $matchId,
                'chatMessages' => $chatMessages,
                'hlsStreams' => $hlsStreams,
                'iframeStreams' => $iframeStreams,
                'dashStreams' => $dashStreams,
                'tv_links' => $tvLinks,
                'matchName' => $matchName,
                'proxyBaseUrl' => $proxyBaseUrl,
                'user' => Auth::user()
            ]);

        } catch (\Exception $e) {
            Log::error("Error di StreamController: " . $e->getMessage());
            abort(500, 'Terjadi kesalahan pada server.');
        }
    }

    /**
     * <<< PERUBAHAN BARU: Fungsi Proxy untuk mengatasi CORS
     * Fungsi ini akan mengambil data dari URL eksternal dan mengembalikannya sebagai JSON.
     */
    public function proxyStreamManifest()
    {
        $manifestUrl = 'https://carryflix.com/streams.json';

        try {
            $response = Http::timeout(10)->get($manifestUrl);

            if ($response->successful()) {
                // Kembalikan konten JSON langsung dengan header yang benar
                return response($response->body())
                      ->header('Content-Type', 'application/json');
            }

            // Jika gagal, kembalikan response error
            return response()->json(['error' => 'Gagal mengambil manifest stream dari sumber.'], $response->status());

        } catch (\Exception $e) {
            Log::error("Error di proxyStreamManifest: " . $e->getMessage());
            return response()->json(['error' => 'Kesalahan server saat mem-proxy request.'], 500);
        }
    }
}

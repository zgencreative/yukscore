<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use DateTime;
use DateTimeZone;
use App\Http\Controllers\ApiController;

class DetailMatchController extends Controller
{
    public function show($country, $comp, $idMatch, Request $request)
    {
        $apiController = new ApiController();
        $data = $apiController->getDetailMatch($idMatch);
        $data = $data->getData(true);

        // Parsing timestamp
        $timestampStr = strval($data['data']['time_start']);
        $timestamp = DateTime::createFromFormat('YmdHis', $timestampStr, new DateTimeZone('UTC'));

        // Convert ke Asia/Bangkok
        $timestamp->setTimezone(new DateTimeZone('Asia/Bangkok'));
        $formattedTimestamp = $timestamp->format('YmdHis');

        $data['data']['time_start'] = $this->convertTime($formattedTimestamp);
        $host = $request->getHost();

        $statusPertandingan = $data['data']['Status_Match'];

        $statusValid = [
            'FT', 'AP', 'AET', 'Aband.', 'AW', 'ToFi', 
            'NS', 'Postp.', 'Canc.'
        ];
        $hasLive = false;

        if (!in_array($statusPertandingan, $statusValid)) {
            $hasLive = $this->findLive($idMatch);
        }

        return view('detail_match', [
            'has_live' => $hasLive,
            'data' => $data,
            'host' => $host,
            'page_name' => 'match'
        ]);
    }

    private function convertTime($timestamp)
    {
        $dt = DateTime::createFromFormat('YmdHis', $timestamp);
        return $dt->format('d.m.Y H:i');
    }

    private function findLive($idMatch)
    {
        $proxyBaseUrl = config('services.stream_proxy.base_url');
        $apiUrl = "{$proxyBaseUrl}/schedule";
        
        try {
            // Melakukan request GET ke URL
            $response = Http::get($apiUrl);

            // Memeriksa apakah request berhasil (status code 200-299)
            if ($response->successful()) {
                // Ambil data JSON dan ubah menjadi array PHP
                $scheduleData = $response->json();

                // Pastikan data yang diterima adalah array
                if (!is_array($scheduleData)) {
                    Log::warning('API schedule tidak mengembalikan format array yang valid.');
                    return null;
                }

                // --- BAGIAN UTAMA PERBAIKAN ---
                // 1. Ubah array menjadi Laravel Collection untuk mempermudah pencarian.
                // 2. Gunakan metode `firstWhere` untuk menemukan item pertama
                //    yang memiliki 'id_match' sesuai dengan parameter $idMatch.
                $foundMatch = collect($scheduleData)->firstWhere('id_match', $idMatch);
                
                // 3. Kembalikan hasilnya. Jika tidak ditemukan, `firstWhere` akan
                //    secara otomatis mengembalikan `null`.
                return $foundMatch;

            } else {
                // Jika request gagal, catat error untuk debugging
                Log::error('Gagal mengambil data dari API schedule.', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return null;
            }

        } catch (\Exception $e) {
            // Menangani error jika request gagal total (misal: koneksi putus)
            Log::error('Terjadi exception saat request ke API schedule.', [
                'message' => $e->getMessage()
            ]);
            return null;
        }
    }
}


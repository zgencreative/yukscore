<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\ApiController;
use Carbon\Carbon; // <-- Tambahkan ini untuk manipulasi waktu

class HomeController extends Controller
{
    public function index()
    {
        // Bagian kode Anda yang sudah ada (tetap dipertahankan)
        $sortedController = new ApiController();
        $response = $sortedController->sortedData();
        $sortedData = $response->getData(true);

        // ===============================================
        // ++ TAMBAHKAN LOGIKA BARU UNTUK SIDEBAR DI SINI ++
        // ===============================================
        $upcomingMatches = collect([]); // Siapkan koleksi kosong sebagai default

        try {
            // Asumsi URL API Anda, sesuaikan jika perlu dari config
            $scheduleApiUrl = config('services.stream_proxy.base_url') . '/schedule';
            $scheduleResponse = Http::get($scheduleApiUrl);

            if ($scheduleResponse->successful()) {
                $scheduleData = $scheduleResponse->json();
                $now = Carbon::now();

                // Filter data untuk mendapatkan pertandingan yang akan datang
                $upcomingMatches = collect($scheduleData)
                    ->filter(function ($event) use ($now) {
                        if (empty($event['start_time'])) {
                            return false;
                        }
                        $startTime = Carbon::createFromFormat('YmdHis', $event['start_time']);
                        return $startTime->isFuture();
                    })
                    ->sortBy('start_time'); // Urutkan dari yang paling dekat
            }
        } catch (\Exception $e) {
            // Jika API gagal, biarkan $upcomingMatches kosong agar halaman tidak error
            // Anda bisa menambahkan Log::error() di sini jika perlu
        }
        // ===============================================
        // -- AKHIR LOGIKA BARU --
        // ===============================================

        // Kirim semua data ke view home
        return view('home', [
            'liveMatches' => $sortedData['live'],
            'previousMatches' => $sortedData['previous'],
            'nextMatches' => $sortedData['next'],
            'upcomingMatches' => $upcomingMatches, // <-- Tambahkan data baru ini
        ]);
    }
}
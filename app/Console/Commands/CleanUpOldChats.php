<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ChatMessage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CleanUpOldChats extends Command
{
    protected $signature = 'app:clean-up-old-chats';
    protected $description = 'Menghapus riwayat chat dari pertandingan yang end_time-nya sudah terlewati';

    public function handle()
    {
        $this->info('Memulai proses pembersihan chat lama...');
        try {
            $proxyBaseUrl = config('services.stream_proxy.base_url');
            $scheduleApiUrl = "{$proxyBaseUrl}/schedule";
            $response = Http::get($scheduleApiUrl);

            if ($response->failed()) {
                $this->error('Gagal mengambil data jadwal.');
                return 1;
            }

            $scheduleData = $response->json();
            $now = Carbon::now();

            $finishedMatchIds = collect($scheduleData)
                ->filter(function ($event) use ($now) {
                    if (!isset($event['end_time']) || empty($event['end_time'])) {
                        return false;
                    }
                    // Ubah format YYYYMMDDHHMMSS menjadi objek Carbon
                    $endTime = Carbon::createFromFormat('YmdHis', $event['end_time']);
                    // Cek jika waktu berakhir sudah lewat
                    return $endTime->isPast();
                })
                ->pluck('id_match')
                ->all();

            if (empty($finishedMatchIds)) {
                $this->info('Tidak ada pertandingan berakhir yang ditemukan.');
                return 0;
            }

            $deletedCount = ChatMessage::whereIn('match_id', $finishedMatchIds)->delete();

            if ($deletedCount > 0) {
                $this->info("Pembersihan selesai. {$deletedCount} pesan chat berhasil dihapus.");
                Log::info("Scheduled chat cleanup: {$deletedCount} messages deleted.");
            } else {
                $this->info('Tidak ada pesan chat yang cocok untuk dihapus.');
            }
        } catch (\Exception $e) {
            $this->error("Terjadi error: " . $e->getMessage());
            Log::error("Scheduled chat cleanup failed: " . $e->getMessage());
            return 1;
        }
        return 0;
    }
}
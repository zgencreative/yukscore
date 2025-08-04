<?php

namespace App\Http\Controllers;

use App\Events\NewChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function sendMessage(Request $request, $matchId)
    {
        // 1. Validasi data yang masuk
        $data = $request->validate([
            'sender_name' => 'required|string|max:50',
            'message' => 'required|string|max:1000',
        ]);

        // 2. Panggil broadcast helper dengan data yang sudah divalidasi
        //    Kita hapus ->toOthers() untuk memastikan pesan terkirim ke semua orang saat tes.
        broadcast(new NewChatMessage((object)$data, $matchId))->toOthers();

        // 3. Kembalikan respons JSON
        return response()->json(['status' => 'Message Broadcasted']);
    }
}
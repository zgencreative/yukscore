<?php

namespace App\Http\Controllers;

use App\Events\NewChatMessage;
use Illuminate\Http\Request;
use App\Models\ChatMessage;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function sendMessage(Request $request, $matchId)
    {
        $data = $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $senderName = '';
        if (Auth::check()) {
            // JIKA SUDAH LOGIN: Ambil nama dari user yang terotentikasi
            $senderName = Auth::user()->name;
        } else {
            // JIKA GUEST: Ambil nama dari request (GuestXXXX) dan validasi
            $senderName = $request->validate(['sender_name' => 'required|string|max:50'])['sender_name'];
        }

        $dataToSave = [
            'match_id' => $matchId,
            'sender_name' => $senderName,
            'message' => $data['message'],
        ];

        // 1. Pesan disimpan ke database dan hasilnya ada di variabel $message
        $message = ChatMessage::create($dataToSave);

        // 2. Siarkan variabel $message (bukan $data)
        broadcast(new NewChatMessage($message, $matchId))->toOthers();

        // 3. Kembalikan respons JSON
        return response()->json(['status' => 'Message Saved and Broadcasted']);
    }
}
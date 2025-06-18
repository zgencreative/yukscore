<?php

namespace App\Http\Controllers;

use App\Events\ChatMessageSent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:255',
            'username' => 'required_if:auth.guest|string|max:50', // Username wajib jika guest
        ]);

        $username = "Guest";

        if (Auth::check()) {
            // Jika pengguna sudah login, gunakan nama mereka
            $username = Auth::user()->name;
        } else {
            // Jika tidak, gunakan nama yang dikirim dari form
            $username = $request->input('username', 'Guest' . rand(1000, 9999));
        }

        $message = $request->input('message');

        // Memicu event. toOthers() agar tidak mengirim kembali ke si pengirim.
        broadcast(new ChatMessageSent($username, $message))->toOthers();

        return response()->json(['status' => 'Pesan terkirim!']);
    }
}
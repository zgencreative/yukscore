<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast; // <-- PASTIKAN INI ADA
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

// PASTIKAN ADA "implements ShouldBroadcast"
class NewChatMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    // Properti ini akan menampung data chat
    public object $message;
    public string $matchId;

    // Constructor untuk menerima data dari controller
    public function __construct(object $message, string $matchId)
    {
        $this->message = $message;
        $this->matchId = $matchId;
    }

    // Method untuk menentukan nama channel
    public function broadcastOn(): array
    {
        return [new Channel("stream-chat.{$this->matchId}")];
    }

    // Method untuk menentukan nama event
    public function broadcastAs(): string
    {
        return 'new-message';
    }
}
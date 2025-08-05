<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->string('match_id')->index(); // Tambahkan index untuk pencarian cepat
            $table->string('sender_name');
            $table->text('message');
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('chat_messages');
    }
};

<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\DetailMatchController;
use App\Http\Controllers\DetailCompController;
use App\Http\Controllers\DetailTeamController;
use App\Http\Controllers\StreamController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\MobileStreamController;

Route::get('/', [HomeController::class, 'index'])->name('index');

Route::post('/chat/{matchId}', [ChatController::class, 'sendMessage'])->name('chat.send');

Route::get('/match/{country}/{comp}/{idMatch}', [DetailMatchController::class, 'show']);

Route::get('/comp/{country}/{comp}', [DetailCompController::class, 'show'])->name('comp.detail');

Route::get('/team/{idTeam}', [DetailTeamController::class, 'show'])->name('team.detail');

Route::get('/stream/{matchId}', [StreamController::class, 'playById'])->name('stream.play');

Route::get('/proxy/streams', [App\Http\Controllers\StreamController::class, 'proxyStreamManifest']);

Route::get('/mobile-stream', [MobileStreamController::class, 'showIframe']);

//BACKEND API

// Route::prefix('api')->group(function () {
    
// });


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

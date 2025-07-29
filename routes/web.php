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

Route::get('/', [HomeController::class, 'index'])->name('index');;

Route::get('/match/{country}/{comp}/{idMatch}', [DetailMatchController::class, 'show']);

Route::get('/comp/{country}/{comp}', [DetailCompController::class, 'show'])->name('comp.detail');

Route::get('/team/{idTeam}', [DetailTeamController::class, 'show'])->name('team.detail');

Route::get('/stream/{matchId}', [StreamController::class, 'playById'])->name('stream.play');

Route::get('/proxy/streams', [App\Http\Controllers\StreamController::class, 'proxyStreamManifest']);

//BACKEND API

Route::prefix('api')->group(function () {
    Route::get('/football/detailCountry/{country}', [ApiController::class, 'detailCountry']);
    Route::get('/football/{date}', [ApiController::class, 'footballByDate']);
    Route::get('/sorted-data/{date?}', [ApiController::class, 'sortedData']);
    Route::get('/search/{keyword?}', [ApiController::class, 'search']);

    Route::prefix('football/detailMatch')->group(function () {
        Route::get('/{idMatch}', [ApiController::class, 'getDetailMatch']);
        Route::get('/stat/{idMatch}', [ApiController::class, 'getMatchStat']);
        Route::get('/lineup/{idMatch}', [ApiController::class, 'getMatchLineup']);
        Route::get('/table/{urlComp}', [ApiController::class, 'getLeagueTable']);
        Route::get('/news/{idMatch}', [ApiController::class, 'getMatchNews']);
        Route::get('/info/{id}', [ApiController::class, 'getDetailMatchInfo']);
    });

    Route::prefix('football/detailComp')->group(function () {
        Route::get('/{urlComp}', [ApiController::class, 'getCompetitionDetail']);
    });

    Route::prefix('football/detailTeam')->group(function () {
        Route::get('/{idTeam}', [ApiController::class, 'getTeamDetail']);
        Route::get('/playerstat/{teamId}/{eventId?}', [ApiController::class, 'getPlayerStat']);
        Route::get('/squad/{id}', [ApiController::class, 'getTeamSquad']);
        Route::get('/stat/{teamId}/{eventId?}', [ApiController::class, 'getTeamStat']);
        Route::get('/table/{id}', [ApiController::class, 'getTeamTable']);
        Route::get('/news/{idTeam}', [ApiController::class, 'getTeamNews']);
    });
});


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

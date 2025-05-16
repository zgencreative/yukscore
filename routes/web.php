<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\DetailMatchController;
use App\Http\Controllers\DetailCompController;
use App\Http\Controllers\DetailTeamController;


// FE

Route::get('/', [HomeController::class, 'index']);

Route::get('/match/{country}/{comp}/{idMatch}', [DetailMatchController::class, 'show']);

Route::get('/comp/{country}/{comp}', [DetailCompController::class, 'show'])->name('comp.detail');

Route::get('/team/{idTeam}', [DetailTeamController::class, 'show'])->name('team.detail');

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

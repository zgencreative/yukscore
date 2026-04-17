<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/football/detailCountry/{country}', [ApiController::class, 'detailCountry']);
    Route::post('/football/favorit', [ApiController::class, 'getMatchFavorite']);
    Route::get('/football/{date}', [ApiController::class, 'footballByDate']);
    Route::get('/sorted-data/{date?}', [ApiController::class, 'sortedData']);
    Route::get('/search/{keyword?}/{type?}', [ApiController::class, 'search']);

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
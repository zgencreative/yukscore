<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\ApiController;

class HomeController extends Controller
{
    public function index()
    {
        // Panggil controller lain
        $sortedController = new ApiController();

        // Ambil data sorted (tanpa parameter = hari ini)
        $response = $sortedController->sortedData();

        // Ambil isi JSON dari Response
        $sortedData = $response->getData(true); // jadi array

        // Kirim ke view home
        return view('home', [
            'liveMatches' => $sortedData['live'],
            'previousMatches' => $sortedData['previous'],
            'nextMatches' => $sortedData['next'],
        ]);
    }
}

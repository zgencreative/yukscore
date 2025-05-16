<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DetailTeamController extends Controller
{
    public function show($idTeam, Request $request)
    {
        try {
            $apiController = new ApiController();
            $data = $apiController->getTeamDetail($idTeam);
            $data = $data->getData(true);

            return view('detail_team', [
                'data' => $data,
                'host' => $request->getHttpHost(),
                'page_name' => 'team'
            ]);
        } catch (\Exception $e) {
            abort(500, 'Error fetching team data: ' . $e->getMessage());
        }
    }
}

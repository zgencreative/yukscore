<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\ApiController;

class DetailCompController extends Controller
{
    public function show($country, $comp)
    {
        $urlComp = "$country.$comp";
        $apiController = new ApiController();
        $data = $apiController->getCompetitionDetail($urlComp);
        $data = $data->getData(true);

        // Pisahkan event berdasarkan status
        $upcome = [];
        $ns = [];

        foreach ($data['data']['Events'] as $event) {
            if ($event['Status_Match'] === 'FT') {
                $upcome[] = $event;
            } else {
                $ns[] = $event;
            }
        }

        // Urutkan dan gabungkan
        $upcome = array_reverse($upcome);
        $data['data']['Events'] = array_merge($upcome, $ns);

        $host = request()->getHttpHost();
        return view('detail_comp', [
            'data' => $data,
            'host' => $host,
            'page_name' => 'comp',
        ]);
    }

}

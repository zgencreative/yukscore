<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use DateTime;
use DateTimeZone;
use App\Http\Controllers\ApiController;

class DetailMatchController extends Controller
{
    public function show($country, $comp, $idMatch, Request $request)
    {
        $apiController = new ApiController();
        $data = $apiController->getDetailMatch($idMatch);
        $data = $data->getData(true);

        // Parsing timestamp
        $timestampStr = strval($data['data']['time_start']);
        $timestamp = DateTime::createFromFormat('YmdHis', $timestampStr, new DateTimeZone('UTC'));

        // Convert ke Asia/Bangkok
        $timestamp->setTimezone(new DateTimeZone('Asia/Bangkok'));
        $formattedTimestamp = $timestamp->format('YmdHis');

        $data['data']['time_start'] = $this->convertTime($formattedTimestamp);
        $host = $request->getHost();

        return view('detail_match', [
            'data' => $data,
            'host' => $host,
            'page_name' => 'match'
        ]);
    }

    private function convertTime($timestamp)
    {
        $dt = DateTime::createFromFormat('YmdHis', $timestamp);
        return $dt->format('d.m.Y H:i');
    }
}

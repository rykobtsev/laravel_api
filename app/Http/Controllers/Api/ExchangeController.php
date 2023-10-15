<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ExchangeController extends Controller
{
    public function valData(Request $request)
    {
        $request->validate([
            'from_date' => ['sometimes', 'date'],
            'to_date'   => ['sometimes', 'date'],
            'val_code'  => ['sometimes', 'string', 'default:usd'],
        ]);



        $fromDate = \Carbon\Carbon::parse($request->from_date)->format('Ymd');
        $toDate   = \Carbon\Carbon::parse($request->to_date)->format('Ymd');

        $cUrlHttp = "https://bank.gov.ua/NBU_Exchange/exchange_site?";
        $cUrlHttp .= "start=" . $fromDate;
        $cUrlHttp .= "&end=" . $toDate;
        $cUrlHttp .= "&valcode=" . $request->val_code;
        $cUrlHttp .= "&sort=exchangedate&order=desc&json";

        $response = Http::get($cUrlHttp);

        if ($response->successful()) {
            $data = $response->json();

            return response()->json($data, 200);
        } else {
            $errorMessage = $response->body();

            return response()->json($errorMessage, 400);
        }
    }
}

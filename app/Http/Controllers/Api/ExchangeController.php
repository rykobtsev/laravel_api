<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Post(
 *     path="/api/val",
 *     summary="Exchange currency API",
 *     operationId="exchangeAPI",
 *     tags={"External APIs"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         description="Exchange API data",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="from_date", type="string", format="date", example="2023-10-15", description="Date from in YYYY-MM-DD format"),
 *             @OA\Property(property="to_date", type="string", format="date", example="2023-10-15", description="Date to in YYYY-MM-DD format"),
 *             @OA\Property(property="val_code", type="string", example="usd",  description="Currency code."),
 *          )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="List of currencies",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 type="object",
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="field", type="string"),
 *             @OA\Property(property="message", type="string"),
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Bad Request",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="error", type="string", example="Bad Request"),
 *             @OA\Property(property="message", type="string", example="The request was malformed or invalid."),
 *         )
 *     )
 *  )
 */
class ExchangeController extends Controller
{
    public function valData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from_date' => ['sometimes', 'date'],
            'to_date'   => ['sometimes', 'date'],
            'val_code'  => ['sometimes', 'string'],
        ]);

        if ($validator->fails()) {

            $errors = $validator->errors();

            return response()->json([
                'error' => $errors
            ], 400);
        }

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
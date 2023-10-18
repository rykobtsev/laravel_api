<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


/**
 * @OA\Schema(
 *    schema="ExchangeSchema",
 *    @OA\Property(
 *        property="from_date",
 *        description="From date (optional)",
 *        type="string",
 *        format="date",
 *        example="2023-01-01"
 *    ),
 *    @OA\Property(
 *        property="to_date",
 *        description="To date (optional)",
 *        type="string",
 *        format="date",
 *        example="2023-01-31"
 *    ),
 *    @OA\Property(
 *        property="val_code",
 *        description="Valuation code (optional)",
 *        type="string",
 *        example="usd",
 *        default="usd"
 *    ),
 * )
 */
class ExchangeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }
}
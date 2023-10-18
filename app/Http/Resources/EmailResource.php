<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *    schema="EmailSchema",
 *        @OA\Property(
 *            property="to_email",
 *            description="To email",
 *            type="string",
 *            format="email",
 *            nullable="false",
 *            example="test_user@test.com"
 *        ),
 *        @OA\Property(
 *            property="title",
 *            description="Title email",
 *            type="string",
 *            example="Title email"
 *        ),
 *        @OA\Property(
 *            property="text",
 *            description="Text content email",
 *            type="string",
 *            example="Text content email"
 *        ),
 *        @OA\Property(
 *            property="attachments",
 *            description="Array of file attachments to email. Supports file types: pdf, docx, doc, jpg, jpeg, png, gif",
 *            type="array",
 *            @OA\Items(
 *                 type="string",
 *                 format="binary",
 *                 description="Array of file attachments",
 *                 example="base64-encoded-file-data"
 *             ),
 *        ),
 *    )
 * )
 */

class EmailResource extends JsonResource
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
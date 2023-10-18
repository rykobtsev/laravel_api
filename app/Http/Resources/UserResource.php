<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *    schema="UserSchema",
 *        @OA\Property(
 *            property="name",
 *            description="User name",
 *            type="string",
 *            nullable="false",
 *            example="Test User"
 *        ),
 *        @OA\Property(
 *            property="email",
 *            description="User E-mail",
 *            type="string",
 *            format="email",
 *            nullable="false",
 *            example="test_user@test.com"
 *        ),
 *        @OA\Property(
 *            property="password",
 *            description="User password",
 *            type="string",
 *            format="password",
 *            nullable="false",
 *            example="your_password"
 *        ),
 *        @OA\Property(
 *            property="role",
 *            description="User role",
 *            type="string",
 *            format="role",
 *            nullable="false",
 *            example="guest"
 *        ),
 *    )
 * )
 */
class UserResource extends JsonResource
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
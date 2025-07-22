<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     title="CreatedResource",
 *     description="Resource representing a successful creation",
 *     @OA\Property(
 *         property="id",
 *         type="string",
 *         description="The ID of the created resource",
 *         example="99419b32-76e8-4cc6-bd70-e55e17e76ed1"
 *     ),
 * )
 */
class CreatedResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
        ];
    }
}

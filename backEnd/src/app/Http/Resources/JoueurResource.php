<?php

namespace App\Http\Resources;

use App\Models\Experience;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    title: "JoueurResource",
    description: "Resource representing a player",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "user", ref: "#/components/schemas/UserResource"),
        new OA\Property(
            property: "experiences",
            type: "array",
            items: new OA\Items(ref: "#/components/schemas/ExperienceResource")
        )
    ]
)]
class JoueurResource extends JsonResource
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
            'user' => $this->user ? new UserResource($this->user) : null,
            'experiences' => $this->experiences ? ExperienceResource::collection($this->experiences) : [],
        ];
    }
}

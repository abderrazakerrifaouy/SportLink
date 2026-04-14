<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    title: "TitreResource",
    description: "Resource representing a title won by a club admin",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "nomTitre", type: "string", example: "UEFA Champions League"),
        new OA\Property(property: "description", type: "string", example: "Won the UEFA Champions League in 2020"),
        new OA\Property(property: "number", type: "integer", example: 1)
    ]
)
]
class TitreResource extends JsonResource
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
            'nomTitre' => $this->nomTitre,
            'description' => $this->description,
            'number' => $this->number
        ];
    }
}


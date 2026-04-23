<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    title: "ExperienceResource",
    description: "Resource representing a player's experience",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "idClub", type: "integer", example: 1),
        new OA\Property(property: "image", type: "string", nullable: true, example: "https://example.com/logo.png"),
        new OA\Property(property: "nomClub", type: "string", example: "FC Barcelona"),
        new OA\Property(property: "joinDate", type: "string", format: "date", example: "2020-01-01"),
        new OA\Property(property: "endDate", type: "string", format: "date", example: "2022-12-31"),
        new OA\Property(property: "place", type: "string", example: "Spain"),
        new OA\Property(property: "categoryType", type: "string", example: "Professional"),
        new OA\Property(property: "joueur_id", type: "integer", example: 1)
    ]
)]

class ExperienceResource extends JsonResource
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
            'idClub' => $this->idClub,
            'image' => $this->image,
            'nomClub' => $this->clubAdmin?->nomClub,
            'joinDate' => $this->joinDate,
            'endDate' => $this->endDate,
            'place' => $this->place,
            'categoryType' => $this->categoryType,
            'joueur_id' => $this->joueur_id
        ];
    }
}

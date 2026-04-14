<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "ClubAdminResource", // Darori zid had l-ligne bach Swagger i-lqah
    title: "ClubAdminResource",
    description: "Resource representing a club admin",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "user", ref: "#/components/schemas/UserResource"),
        new OA\Property(property: "nomClub", type: "string", example: "FC Barcelona"),
        new OA\Property(property: "description", type: "string", example: "Club admin for FC Barcelona"),
        new OA\Property(property: "ecole", type: "string", example: "Ecole de Foot"),
        new OA\Property(property: "tactique", type: "string", example: "4-4-2"),
        new OA\Property(property: "gestion", type: "string", example: "Professional management"),
        new OA\Property(
            property: "titres",
            type: "array",
            items: new OA\Items(ref: "#/components/schemas/TitreResource")
        ),
        new OA\Property(
            property: "joueurs",
            type: "array",
            items: new OA\Items(ref: "#/components/schemas/JoueurResource")
        )
    ]
)]
class ClubAdminResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => new UserResource($this->user),
            'nomClub' => $this->nomClub,
            'description' => $this->description,
            'ecole' => $this->ecole,
            'tactique' => $this->tactique,
            'gestion' => $this->gestion,
            'titres' => TitreResource::collection($this->titres),
        ];
    }
}

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
        ),
        new OA\Property(
            property: "posts",
            type: "array",
            items: new OA\Items(ref: "#/components/schemas/PostResource")
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
            'user' => $this->user ? new UserResource($this->user) : null,
            'nomClub' => $this->nomClub,
            'description' => $this->description,
            'ecole' => $this->ecole,
            'tactique' => $this->tactique,
            'gestion' => $this->gestion,
            'titres' => TitreResource::collection($this->titres),
            'joueurs' => $this->joueurs ? $this->joueurs->map(function ($joueur) {
                return [
                    'id' => $joueur->id,
                    'user' => [
                        'id' => $joueur->user->id,
                        'name' => $joueur->user->name,
                        'email' => $joueur->user->email,
                        'profile_photo_path' => $joueur->user->profile_photo_path,
                    ],
                ];
            })->toArray() : [],
            'posts' => \App\Http\Resources\PosetResource::collection($this->user ? $this->user->posts : collect()),
        ];
    }
}

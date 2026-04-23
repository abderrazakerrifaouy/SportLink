<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ClubJoueurRequestResource',
    title: 'ClubJoueurRequestResource',
    description: 'Resource representing a club-player request history item',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'status', type: 'string', example: 'PENDING'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2026-04-23T10:00:00Z'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', example: '2026-04-23T10:00:00Z'),
    ]
)]
class ClubJoueurRequestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'joueur_id' => $this->joueur_id,
            'club_admin_id' => $this->club_admin_id,
            'experience_id' => $this->experience_id,
            'joueur' => $this->joueur ? [
                'id' => $this->joueur->id,
                'user' => $this->joueur->user ? [
                    'id' => $this->joueur->user->id,
                    'name' => $this->joueur->user->name,
                    'email' => $this->joueur->user->email,
                    'profileImage' => $this->joueur->user->profileImage,
                ] : null,
            ] : null,
            'club' => $this->club ? [
                'id' => $this->club->id,
                'nomClub' => $this->club->nomClub,
                'description' => $this->club->description,
                'ecole' => $this->club->ecole,
                'user' => $this->club->user ? [
                    'id' => $this->club->user->id,
                    'name' => $this->club->user->name,
                    'email' => $this->club->user->email,
                    'profileImage' => $this->club->user->profileImage,
                ] : null,
            ] : null,
            'experience' => $this->experience ? [
                'id' => $this->experience->id,
                'idClub' => $this->experience->idClub,
                'nomClub' => $this->experience->clubAdmin?->nomClub,
                'joinDate' => $this->experience->joinDate,
                'endDate' => $this->experience->endDate,
                'place' => $this->experience->place,
                'categoryType' => $this->experience->categoryType,
            ] : null,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}

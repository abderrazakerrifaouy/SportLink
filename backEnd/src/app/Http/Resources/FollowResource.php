<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "FollowResource", 
    title: "Follow Resource",
    description: "Resource representation of a Follow relationship",
    type: "object",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "follower_id", ref: "#/components/schemas/UserResource"),
        new OA\Property(property: "following_id", ref: "#/components/schemas/UserResource"),
        new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2024-01-01T12:00:00Z"),
        new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2024-01-02T12:00:00Z"),
    ]
)]

class FollowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return 
        [
            'id' => $this->id,
            'follower_id' => new UserResource($this->follower),
            'following_id' => new UserResource($this->following),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]
        ;
    }
}

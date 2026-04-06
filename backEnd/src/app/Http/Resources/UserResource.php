<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    title: "User Resource",
    description: "Resource representation of a User",
    type: "object",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "name", type: "string", example: "John Doe"),
        new OA\Property(property: "email", type: "string", example: "john@example.com"),
        new OA\Property(property: "bio", type: "string", example: "This is John's bio"),
        new OA\Property(property: "profileImage", type: "string", example: "http://localhost:8081/storage/profile_images/john.jpg"),
        new OA\Property(property: "bannerImage", type: "string", example: "http://localhost:8081/storage/banner_images/john.jpg"),
        new OA\Property(property: "role", type: "string", example: "Joueur"),
        new OA\Property(property: "isActive", type: "boolean", example: true),
        new OA\Property(property: "stats", type: "object", properties: [
            new OA\Property(property: "followers_count", type: "integer", example: 100),
            new OA\Property(property: "following_count", type: "integer", example: 50),
            new OA\Property(property: "posts_count", type: "integer", example: 20),
        ]),
        new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2024-01-01T12:00:00Z"),
        new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2024-01-02T12:00:00Z"),
    ]
)]
class UserResource extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email,
            'bio' => $this->bio,
            'profileImage' => $this->profileImage ? asset('storage/' . $this->profileImage) : null,
            'bannerImage' => $this->bannerImage ? asset('storage/' . $this->bannerImage) : null,
            'role' => $this->role,
            'stats' => [
                'followers_count' => $this->followers()->count(),
                'following_count' => $this->following()->count(),
                'posts_count' => $this->posts()->count(),
            ],
            'isActive' => $this->isActive,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

        ];

    }

}

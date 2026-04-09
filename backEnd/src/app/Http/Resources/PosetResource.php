<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "PostResource",
    title: "Post Resource",
    description: "Resource representation of a Post",
    type: "object",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "content", type: "string", example: "This is a post content"),
        new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2024-01-01T12:00:00Z"),
        new OA\Property(property: "author", ref: "#/components/schemas/UserResource"),
        new OA\Property(property: "media", type: "array", items: new OA\Items(ref: "#/components/schemas/MediaResource")),
        new OA\Property(property: "reactions_summary", type: "object", properties: [
            new OA\Property(property: "total", type: "integer", example: 100),
            new OA\Property(property: "likes", type: "integer", example: 60),
            new OA\Property(property: "dislikes", type: "integer", example: 10),
            new OA\Property(property: "loves", type: "integer", example: 20),
            new OA\Property(property: "wows", type: "integer", example: 5),
            new OA\Property(property: "hahas", type: "integer", example: 5),
        ]),
        new OA\Property(property: "comments_count", type: "integer", example: 15),
    ]
)]
class PosetResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'content' => $this->content,
            'created_at' => $this->created_at->diffForHumans(),
            'author' => new UserResource($this->whenLoaded('user')),
            'media' => MediaResource::collection($this->whenLoaded('media')),
            'reactions_summary' => [
                'total' => $this->reactions()->count(),
                'likes' => $this->reactions()->where('type', 'LIKE')->count(),
                'dislikes' => $this->reactions()->where('type', 'DISLIKE')->count(),
                'loves' => $this->reactions()->where('type', 'LOVE')->count(),
                'wows' => $this->reactions()->where('type', 'WOW')->count(),
                'hahas' => $this->reactions()->where('type', 'HAHA')->count(),
            ],
            'comments_count' => $this->comments()->count(),
        ];
    }
}

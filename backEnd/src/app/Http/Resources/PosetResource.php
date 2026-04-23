<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "PostResource",
    title: "Post Resource",
    description: "Resource representation of a Post",
    type: "object",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "content", type: "string", example: "This is a post content"),
        new OA\Property(property: "created_at", type: "string", example: "2 hours ago"),
        new OA\Property(property: "author", type: "object", properties: [
            new OA\Property(property: "id", type: "integer"),
            new OA\Property(property: "name", type: "string"),
            new OA\Property(property: "profile_image", type: "string")
        ]),
        new OA\Property(property: "media", type: "array", items: new OA\Items(ref: "#/components/schemas/MediaResource")),
        new OA\Property(property: "reactions_summary", type: "object", properties: [
            new OA\Property(property: "total", type: "integer"),
            new OA\Property(property: "likes", type: "integer"),
            new OA\Property(property: "loves", type: "integer"),
            new OA\Property(property: "hahas", type: "integer"),
            new OA\Property(property: "wows", type: "integer"),
            new OA\Property(property: "sads", type: "integer"),
            new OA\Property(property: "grrs", type: "integer"),
        ]),
        new OA\Property(property: "comments_count", type: "integer", example: 15),
        new OA\Property(property: "user_reaction", type: "string", nullable: true, example: "LIKE"),
    ]
)]
class PosetResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Kan-e-utiliser auth('sanctum') bach n-detectiw l-user li m-connecti f l-API
        $currentUser = auth('sanctum')->user();

        return [
            'id' => $this->id,
            'content' => $this->content,
            'created_at' => $this->created_at->diffForHumans(),

            // Sync m3a l-Front-end (User data)
            'author' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'profile_image' => $this->user->profileImage, // Kat-matchi l-key f Vue.js
            ],

            // Media collection (T-akked bli MediaResource hta hiya m9ada)
            'media' => MediaResource::collection($this->whenLoaded('media')),

            // Summary dyal l-reactions (Optimized counts)
            'reactions_summary' => [
                'total' => $this->reactions()->count(),
                'likes' => $this->reactions()->where('type', 'LIKE')->count(),
                'loves' => $this->reactions()->where('type', 'LOVE')->count(),
                'hahas' => $this->reactions()->where('type', 'HAHA')->count(),
                'wows' => $this->reactions()->where('type', 'WOW')->count(),
                'sads' => $this->reactions()->where('type', 'SAD')->count(),
                'grrs' => $this->reactions()->where('type', 'GRR')->count(),
                'dislikes' => $this->reactions()->where('type', 'DISLIKE')->count(),
            ],

            // Count dyal l-comments
            'comments_count' => $this->comments()->count(),

            // Bach l-Front-end ye-3ref l-user ach mn reaction dar (ila darha)
            'user_reaction' => $currentUser
                ? $this->reactions()->where('user_id', $currentUser->id)->first()?->type
                : null,
        ];
    }
}

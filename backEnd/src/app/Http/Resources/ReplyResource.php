<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "ReplyResource",
    title: "Reply Resource",
    description: "Resource representation of a Reply",
    type: "object",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "content", type: "string", example: "This is a reply."),
        new OA\Property(property: "comment_id", type: "integer", example: 1),
        new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2024-01-01T12:00:00Z"),
        new OA\Property(property: "author", ref: "#/components/schemas/UserResource"),
        new OA\Property(property: "reactions_count", type: "integer", example: 3),
    ]
)]
class ReplyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $currentUser = auth('sanctum')->user();

        return [
            'id' => $this->id,
            'content' => $this->content,
            'comment_id' => $this->comment_id,
            'created_at' => $this->created_at->diffForHumans(),
            'user' => new UserResource($this->whenLoaded('user')),
            'reactions_summary' => [
                'total' => $this->reactions()->count(),
                'likes' => $this->reactions()->where('type', 'LIKE')->count(),
                'dislikes' => $this->reactions()->where('type', 'DISLIKE')->count(),
                'loves' => $this->reactions()->where('type', 'LOVE')->count(),
                'wows' => $this->reactions()->where('type', 'WOW')->count(),
                'hahas' => $this->reactions()->where('type', 'HAHA')->count(),
                'sads' => $this->reactions()->where('type', 'SAD')->count(),
                'grrs' => $this->reactions()->where('type', 'GRR')->count(),
            ],
            'user_reaction' => $currentUser
                ? $this->reactions()->where('user_id', $currentUser->id)->first()?->type
                : null,
        ];
    }
}

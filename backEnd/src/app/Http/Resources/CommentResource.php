<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "CommentResource",
    title: "Comment Resource",
    description: "Resource representation of a Comment",
    type: "object",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "content", type: "string", example: "This is a comment."),
        new OA\Property(property: "user", ref: "#/components/schemas/UserResource"),
        new OA\Property(property: "replies", type: "array", items: new OA\Items(ref: "#/components/schemas/ReplyResource")),
        new OA\Property(property: "reactions_count", type: "integer", example: 5),
    ]
)]

class CommentResource extends JsonResource
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
            'user' => new UserResource($this->whenLoaded('user')),
            'replies' => ReplyResource::collection($this->whenLoaded('replies')),
             'reactions_summary' => [
                'total' => $this->reactions()->count(),
                'likes' => $this->reactions()->where('type', 'LIKE')->count(),
                'dislikes' => $this->reactions()->where('type', 'DISLIKE')->count(),
                'loves' => $this->reactions()->where('type', 'LOVE')->count(),
                'wows' => $this->reactions()->where('type', 'WOW')->count(),
                'hahas' => $this->reactions()->where('type', 'HAHA')->count(),
            ],
        ];
    }
}

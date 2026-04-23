<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "ReactionResource",
    title: "Reaction Resource",
    description: "Resource representation of a Reaction",
    type: "object",
    properties: [
        new OA\Property(property: "type", type: "string", example: "LIKE"),
        new OA\Property(property: "user", ref: "#/components/schemas/UserResource"),
        new OA\Property(
            property: "reactable",
             oneOf: [
                new OA\Schema(ref: "#/components/schemas/PostResource"),
                new OA\Schema(ref: "#/components/schemas/CommentResource"),
            ]
        ),
    ]
)]
class ReactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'type' => $this->type,
            'user' => new UserResource($this->whenLoaded('user')),
            'reactable' => $this->whenLoaded('reactable') ? new JsonResource($this->reactable) : null,
        ];
    }
}

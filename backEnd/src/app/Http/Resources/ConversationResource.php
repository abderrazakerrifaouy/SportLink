<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\MessageResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "ConversationResource", 
    title: "Conversation Resource",
    description: "Resource representation of a Conversation",
    type: "object",
    properties: [
        new OA\Property(
            property: "user", 
            ref: "#/components/schemas/UserResource"
        ),
        new OA\Property(
            property: "messages", 
            type: "array", 
            items: new OA\Items(ref: "#/components/schemas/MessageResource")
        ),
    ]
)]
class ConversationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'user' => new UserResource($this['user']),
            'messages' => MessageResource::collection($this['messages']),
        ];
    }
}
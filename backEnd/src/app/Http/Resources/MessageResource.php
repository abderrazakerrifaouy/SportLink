<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "MessageResource", 
    title: "Message Resource",
    description: "Resource representation of a Message",
    type: "object",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "sender_id", type: "integer", example: 1),
        new OA\Property(property: "receiver_id", type: "integer", example: 2),
        new OA\Property(property: "message", type: "string", example: "Hello, how are you?"),
        new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2024-01-01T12:00:00Z"),
        new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2024-01-02T12:00:00Z"),
    ]
)]
class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sender_id' => $this->sender_id,
            'receiver_id' => $this->receiver_id,
            'message' => $this->message,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
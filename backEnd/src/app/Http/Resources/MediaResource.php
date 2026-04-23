<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "MediaResource",
    title: "Media Resource",
    description: "Resource representation of a Media item",
    type: "object",
    properties: [
        new OA\Property(property: "url", type: "string", example: "http://example.com/media/post_image.jpg"),
        new OA\Property(property: "mediaType", type: "string", example: "IMAGE"),
    ]
)]

class MediaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'url' => $this->url,
            'mediaType' => $this->mediaType,
        ];
    }
}

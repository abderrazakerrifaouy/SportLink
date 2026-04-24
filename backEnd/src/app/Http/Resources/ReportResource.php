<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "ReportResource",
    title: "Report Resource",
    description: "Resource representation of a Report",
    type: "object",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "reporter_id", type: "integer", example: 1),
        new OA\Property(property: "reportable_type", type: "string", example: "App\\Models\\User"),
        new OA\Property(property: "reportable_id", type: "integer", example: 1),
        new OA\Property(property: "reason", type: "string", example: "Inappropriate content"),
        new OA\Property(property: "status", type: "string", example: "pending"),
        new OA\Property(property: "reviewed_by", type: "integer", nullable: true),
        new OA\Property(property: "resolved_at", type: "string", nullable: true),
        new OA\Property(property: "created_at", type: "string"),
        new OA\Property(property: "reporter", ref: "#/components/schemas/UserResource"),
    ]
)]
class ReportResource extends JsonResource
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
            'reporter_id' => $this->reporter_id,
            'reportable_type' => $this->reportable_type,
            'reportable_id' => $this->reportable_id,
            'reason' => $this->reason,
            'status' => $this->status,
            'reviewed_by' => $this->reviewed_by,
            'resolved_at' => $this->resolved_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'reporter' => $this->whenLoaded('reporter', fn () => new UserResource($this->reporter)),
            'reviewer' => $this->whenLoaded('reviewer', fn () => new UserResource($this->reviewer)),
        ];
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReportResource;
use App\Models\Report;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

class ReportController extends Controller
{
    #[OA\Post(
        path: "/reports",
        summary: "Create a report",
        description: "Create a report for a user, post, comment, or reply",
        tags: ["Report"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["reportable_type", "reportable_id", "reason"],
                properties: [
                    new OA\Property(property: "reportable_type", type: "string", example: "App\\Models\\User", description: "The type of resource being reported"),
                    new OA\Property(property: "reportable_id", type: "integer", example: 1, description: "The ID of the resource being reported"),
                    new OA\Property(property: "reason", type: "string", example: "Inappropriate content", description: "The reason for reporting")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Report created successfully",
                content: new OA\JsonContent(ref: "#/components/schemas/ReportResource")
            ),
            new OA\Response(
                response: 422,
                description: "Validation error or report already exists"
            )
        ]
    )]
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'reportable_type' => ['required', 'string'],
            'reportable_id' => ['required', 'integer', 'min:1'],
            'reason' => ['required', 'string', 'min:10', 'max:1000'],
        ]);

        try {
            // Check if user already reported this resource
            $existingReport = Report::where('reporter_id', Auth::id())
                ->where('reportable_type', $validated['reportable_type'])
                ->where('reportable_id', $validated['reportable_id'])
                ->first();

            if ($existingReport) {
                return response()->json(
                    ['message' => 'You have already reported this content'],
                    422
                );
            }

            // Create the report
            $report = Report::create([
                'reporter_id' => Auth::id(),
                'reportable_type' => $validated['reportable_type'],
                'reportable_id' => $validated['reportable_id'],
                'reason' => $validated['reason'],
                'status' => 'pending',
            ]);

            return response()->json(new ReportResource($report), 201);
        } catch (\Exception $exception) {
            return response()->json(
                ['message' => 'Failed to create report: ' . $exception->getMessage()],
                422
            );
        }
    }

    #[OA\Get(
        path: "/reports/my-reports",
        summary: "Get user's reports",
        description: "Get all reports submitted by the authenticated user",
        tags: ["Report"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "List of user's reports",
                content: new OA\JsonContent(type: "array", items: new OA\Items(ref: "#/components/schemas/ReportResource"))
            )
        ]
    )]
    public function myReports(): JsonResponse
    {
        $reports = Report::with(['reporter', 'reviewer', 'reportable'])
            ->where('reporter_id', Auth::id())
            ->orderByDesc('created_at')
            ->get();

        return response()->json(ReportResource::collection($reports));
    }

    #[OA\Get(
        path: "/reports/{id}",
        summary: "Get report details",
        description: "Get details of a specific report",
        tags: ["Report"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "Report ID",
                schema: new OA\Schema(type: "integer")
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Report details",
                content: new OA\JsonContent(ref: "#/components/schemas/ReportResource")
            ),
            new OA\Response(response: 404, description: "Report not found")
        ]
    )]
    public function show($id): JsonResponse
    {
        $report = Report::with(['reporter', 'reviewer', 'reportable'])->find($id);

        if (!$report) {
            return response()->json(['message' => 'Report not found'], 404);
        }

        // Check if user is the reporter or is an admin
        if ($report->reporter_id !== Auth::id() && Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json(new ReportResource($report));
    }
}

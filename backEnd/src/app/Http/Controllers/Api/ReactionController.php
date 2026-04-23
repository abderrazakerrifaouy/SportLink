<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Services\ReactionService;
use OpenApi\Attributes as OA;


class ReactionController extends Controller
{
    protected $reactionService;

    public function __construct(ReactionService $reactionService)
    {
        $this->reactionService = $reactionService;
    }

    #[OA\Post(
        path: "/reactions",
        summary: "Create a new reaction",
        description: "Creates a new reaction for a user on a reactable item",
        tags: ["Reaction"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["type", "reactable_id", "reactable_type"],
                properties: [
                    new OA\Property(property: "type", type: "string", example: "LIKE"),
                    new OA\Property(property: "reactable_id", type: "integer", example: 1),
                    new OA\Property(property: "reactable_type", type: "string", example: "App\\Models\\Post")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Reaction created, updated, or removed successfully"),
            new OA\Response(response: 422, description: "Validation failed")
        ]
    )]

    public function createReaction(Request $request)
    {
        $validatedData = $request->validate([
            'type' => 'required|string|in:LIKE,HAHA,WOW,GRR,SAD,LOVE,DISLIKE',
            'reactable_id' => 'required|integer',
            'reactable_type' => ['required', 'string', Rule::in([
                'Post',
                'Comment',
                'App\\Models\\Post',
                'App\\Models\\Comment',
                'App\\Models\\Reply',
            ])],
        ]);

        return response()->json($this->reactionService->createReaction(
            $validatedData['type'],
            Auth::id(),
            $validatedData['reactable_id'],
            $validatedData['reactable_type']
        ));
    }

    #[OA\Delete(
        path: "/reactions/{id}",
        summary: "Delete a reaction",
        description: "Deletes a reaction by its ID",
        tags: ["Reaction"],
        security: [["bearerAuth" => []]],
    )]
    #[OA\Parameter(
        name: "id",
        in: "path",
        required: true,
        description: "ID of the reaction to delete",
        schema: new OA\Schema(type: "integer")
    )]
    #[OA\Response(response: 200, description: "Reaction deleted successfully")]
    public function deleteReaction($id)
    {
        return response()->json($this->reactionService->deleteReaction($id));
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
                required: ["type", "user_id", "reactable_id", "reactable_type"],
                properties: [
                    new OA\Property(property: "type", type: "string", example: "LIKE"),
                    new OA\Property(property: "user_id", type: "integer", example: 1),
                    new OA\Property(property: "reactable_id", type: "integer", example: 1),
                    new OA\Property(property: "reactable_type", type: "string", example: "Post")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Reaction created successfully"),
            new OA\Response(response: 422, description: "Has already reacted to this item")
        ]
    )]

    public function createReaction(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'type' => 'required|string|in:LIKE,HAHA,WOW,GRR,SAD,LOVE,DISLIKE',
                'user_id' => 'required|integer',
                'reactable_id' => 'required|integer',
                'reactable_type' => 'required|string',
            ]);

        $validatedData = $request->validate([
            'type' => 'required|string|in:LIKE,HAHA,WOW,GRR,SAD,LOVE,DISLIKE',
            'user_id' => 'required|integer',
            'reactable_id' => 'required|integer',
            'reactable_type' => 'required|string',
        ]);

        return response()->json($this->reactionService->createReaction(
            $validatedData['type'],
            $validatedData['user_id'],
            $validatedData['reactable_id'],
            $validatedData['reactable_type']
        ), 201);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
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

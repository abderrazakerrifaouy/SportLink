<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Http\Resources\ReplyResource;
use Illuminate\Http\Request;
use App\Services\CommentService;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;


class CommentController extends Controller
{
    protected $commentService;

    public function __construct(CommentService $commentService)
    {
        $this->commentService = $commentService;
    }

    #[OA\Post(
        path: "/posts/{postId}/comments",
        summary: "Add a comment to a post",
        description: "Adds a comment to the specified post",
        tags: ["Comment"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "postId",
                in: "path",
                required: true,
                description: "ID of the post to comment on",
                schema: new OA\Schema(type: "integer")
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["content"],
                properties: [
                    new OA\Property(property: "content", type: "string", example: "This is a comment")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Comment added successfully",
                content: new OA\JsonContent(ref: "#/components/schemas/CommentResource")
            )
        ]
    )]
    public function addComment(Request $request, $postId)
    {
        $userId = Auth::id();
        $content = $request->input('content');
        return response()->json(new CommentResource($this->commentService->addComment($postId, $userId, $content)));
    }

    #[OA\Post(
        path: "/comments/{commentId}/replies",
        summary: "Add a reply to a comment",
        description: "Adds a reply to the specified comment",
        tags: ["Comment"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "commentId",
                in: "path",
                required: true,
                description: "ID of the comment to reply to",
                schema: new OA\Schema(type: "integer")
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["content"],
                properties: [
                    new OA\Property(property: "content", type: "string", example: "This is a reply")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Reply added successfully",
            )
        ]
    )]

    public function addReply(Request $request, $commentId)
    {
        $userId = Auth::id();
        $content = $request->input('content');
        return response()->json($this->commentService->addReply($commentId, $userId, $content));
    }

    #[OA\Patch(
        path: "/comments/{commentId}",
        summary: "Update a comment",
        description: "Updates the specified comment",
        tags: ["Comment"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "commentId",
                in: "path",
                required: true,
                description: "ID of the comment to update",
                schema: new OA\Schema(type: "integer")
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["content"],
                properties: [
                    new OA\Property(property: "content", type: "string", example: "Updated comment text")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Comment updated successfully")
        ]
    )]

    public function updateComment(Request $request, $id)
    {
        $data = $request->validate([
            'content' => 'required|string|max:5000',
        ]);

        try {
            $comment = $this->commentService->updateComment($id, Auth::id(), $data['content']);
            return response()->json(new CommentResource($comment));
        } catch (\DomainException $exception) {
            $message = strtolower($exception->getMessage());
            $status = str_contains($message, 'not found')
                ? 404
                : (str_contains($message, 'not allowed') ? 403 : 422);

            return response()->json(['message' => $exception->getMessage()], $status);
        }
    }

    #[OA\Patch(
        path: "/replies/{replyId}",
        summary: "Update a reply",
        description: "Updates the specified reply",
        tags: ["Comment"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "replyId",
                in: "path",
                required: true,
                description: "ID of the reply to update",
                schema: new OA\Schema(type: "integer")
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["content"],
                properties: [
                    new OA\Property(property: "content", type: "string", example: "Updated reply text")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Reply updated successfully")
        ]
    )]

    public function updateReply(Request $request, $id)
    {
        $data = $request->validate([
            'content' => 'required|string|max:5000',
        ]);

        try {
            $reply = $this->commentService->updateReply($id, Auth::id(), $data['content']);
            return response()->json(new ReplyResource($reply));
        } catch (\DomainException $exception) {
            $message = strtolower($exception->getMessage());
            $status = str_contains($message, 'not found')
                ? 404
                : (str_contains($message, 'not allowed') ? 403 : 422);

            return response()->json(['message' => $exception->getMessage()], $status);
        }
    }

    #[OA\Delete(
        path: "/comments/{commentId}",
        summary: "Delete a comment",
        description: "Deletes the specified comment",
        tags: ["Comment"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "commentId",
                in: "path",
                required: true,
                description: "ID of the comment to delete",
                schema: new OA\Schema(type: "integer")
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Comment deleted successfully"
            )
        ]
    )]

    public function deleteComment($id)
    {
        try {
            $this->commentService->deleteComment($id, Auth::id());
            return response()->json(['message' => 'Comment deleted successfully']);
        } catch (\DomainException $exception) {
            $message = strtolower($exception->getMessage());
            $status = str_contains($message, 'not found')
                ? 404
                : (str_contains($message, 'not allowed') ? 403 : 422);

            return response()->json(['message' => $exception->getMessage()], $status);
        }
    }

    #[OA\Delete(
        path: "/replies/{replyId}",
        summary: "Delete a reply",
        description: "Deletes the specified reply",
        tags: ["Comment"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "replyId",
                in: "path",
                required: true,
                description: "ID of the reply to delete",
                schema: new OA\Schema(type: "integer")
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Reply deleted successfully"
            )
        ]
    )]
    public function deleteReply($id)
    {
        try {
            $this->commentService->deleteReply($id, Auth::id());
            return response()->json(['message' => 'Reply deleted successfully']);
        } catch (\DomainException $exception) {
            $message = strtolower($exception->getMessage());
            $status = str_contains($message, 'not found')
                ? 404
                : (str_contains($message, 'not allowed') ? 403 : 422);

            return response()->json(['message' => $exception->getMessage()], $status);
        }
    }

    #[OA\Get(
        path: "/posts/{postId}/comments",
        summary: "Get comments for a post",
        description: "Retrieves all comments for the specified post",
        tags: ["Comment"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "postId",
                in: "path",
                required: true,
                description: "ID of the post to get comments for",
                schema: new OA\Schema(type: "integer")
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Comments retrieved successfully"
            )
        ]
    )]

    public function getCommentsByPostId($postId)
    {
        return response()->json(CommentResource::collection($this->commentService->getCommentsByPostId($postId)));
    }

    #[OA\Get(
        path: "/comments/{commentId}/replies",
        summary: "Get replies for a comment",
        description: "Retrieves all replies for the specified comment",
        tags: ["Comment"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "commentId",
                in: "path",
                required: true,
                description: "ID of the comment to get replies for",
                schema: new OA\Schema(type: "integer")
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Replies retrieved successfully"
            )
        ]
    )]
    public function getRepliesByCommentId($commentId)
    {
        return response()->json(ReplyResource::collection($this->commentService->getRepliesByCommentId($commentId)));
    }
}

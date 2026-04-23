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
        $this->commentService->deleteComment($id);
        return response()->json(['message' => 'Comment deleted successfully']);
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
        $this->commentService->deleteReply($id);
        return response()->json(['message' => 'Reply deleted successfully']);
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

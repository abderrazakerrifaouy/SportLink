<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PosetResource;
use Illuminate\Http\Request;
use App\Services\PostService;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;


class PosetController extends Controller
{
    private $postService;
    public function __construct(PostService $postService) {
        $this->postService = $postService;
    }

    #[OA\Post(
        path: '/posts',
        summary: 'Create a new post',
        description: 'Creates a new post with the given content and user ID',
        tags: ['Post'],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'content', type: 'string', example: 'This is a new post'),
                    new OA\Property(property: 'media', type: 'array', items: new OA\Items(ref: '#/components/schemas/MediaResource'))
                ]
            )
        ),

        responses: [
            new OA\Response(
                response: 201,
                description: 'Post created successfully' ,
                content: new OA\JsonContent(ref: '#/components/schemas/PostResource')
            )
        ]
    )]

    public function createPost(Request $request) {
        $content = $request->input('content');
        $media = $request->input('media');
        $userId = Auth::id();
        return response()->json(new PosetResource($this->postService->createPost($content, $userId, $media)));
    }

    #[OA\Get(
        path: '/posts/search',
        summary: 'Search posts',
        description: 'Search posts by content',
        tags: ['Post'],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: 'q',
                in: 'query',
                description: 'Search keyword',
                required: false,
                schema: new OA\Schema(type: 'string')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Posts found',
                content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/PostResource'))
            )
        ]
    )]
    public function searchPosts(Request $request)
    {
        $query = $request->query('q', '');
        return response()->json(PosetResource::collection($this->postService->searchPosts($query)));
    }

    #[OA\Get(
        path: '/posts/{id}',
        summary: 'Get a post by ID',
        description: 'Retrieves a post with the given ID',
        tags: ['Post'],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                description: 'The ID of the post to retrieve',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Post retrieved successfully',
                content: new OA\JsonContent(ref: '#/components/schemas/PostResource')
            )
        ]
    )]


    public function getPostById($id) {
        return response()->json(new PosetResource($this->postService->findPost($id)));
    }

    #[OA\Put(
        path: '/posts/{id}',
        summary: 'Update a post',
        description: 'Updates a post with the given ID',
        tags: ['Post'],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                description: 'The ID of the post to update',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'content', type: 'string', example: 'This is an updated post') ,
                    new OA\Property(property: 'media', type: 'array', items: new OA\Items(ref: '#/components/schemas/MediaResource'))
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Post updated successfully',
                content: new OA\JsonContent(ref: '#/components/schemas/PostResource')
            )
        ]
    )]

    public function updatePost(Request $request, $id) {
        $data = $request->validate([
            'content' => 'required|string|max:5000',
            'media' => 'nullable|array',
            'media.*.url' => 'required_with:media|string|max:255',
            'media.*.mediaType' => 'required_with:media|in:IMAGE,VIDEO',
        ]);

        try {
            $post = $this->postService->updatePost($id, Auth::id(), $data['content'], $data['media'] ?? []);
            return response()->json(new PosetResource($post));
        } catch (\DomainException $exception) {
            $message = strtolower($exception->getMessage());
            $status = str_contains($message, 'not found')
                ? 404
                : (str_contains($message, 'not allowed') ? 403 : 422);

            return response()->json(['message' => $exception->getMessage()], $status);
        }
    }

    #[OA\Delete(
        path: '/posts/{id}',
        summary: 'Delete a post',
        description: 'Deletes a post with the given ID',
        tags: ['Post'],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                description: 'The ID of the post to delete',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Post deleted successfully'
            )
        ]
    )]
    public function deletePost($id) {
        try {
            $this->postService->deletePostByUser($id, Auth::id());
            return response()->json(['message' => 'Post deleted successfully']);
        } catch (\DomainException $exception) {
            $message = strtolower($exception->getMessage());
            $status = str_contains($message, 'not found')
                ? 404
                : (str_contains($message, 'not allowed') ? 403 : 422);

            return response()->json(['message' => $exception->getMessage()], $status);
        }
    }

    #[OA\Get(
        path: '/posts',
        summary: 'Get all posts',
        description: 'Retrieves all posts',
        tags: ['Post'],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Posts retrieved successfully',
                content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/PostResource'))
            )
        ]
    )]
    public function getAllPosts() {
        return response()->json(PosetResource::collection($this->postService->getAllPosts()));
    }

    #[OA\Get(
        path: '/posts/user/{id_user}',
        summary: 'Get posts by user ID',
        description: 'Retrieves posts for the given user ID',
        tags: ['Post'],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: 'id_user',
                in: 'path',
                description: 'The ID of the user whose posts to retrieve',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Posts retrieved successfully',
                content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/PostResource'))

            )
        ]
    )]

    public function getPostsByUserId($id_user){
        return response()->json(PosetResource::collection($this->postService->getPostsByUserId($id_user)));
    }
}

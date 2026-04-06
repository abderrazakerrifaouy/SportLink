<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\PostService;
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
                    new OA\Property(property: 'user_id', type: 'integer', example: 1)
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Post created successfully'
            )
        ]
    )]

    public function createPost(Request $request) {
        $content = $request->input('content');
        $userId = $request->input('user_id');
        return response()->json($this->postService->createPost($content, $userId));
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
                description: 'Post retrieved successfully'
            )
        ]
    )]


    public function getPostById($id) {
        return response()->json($this->postService->findPost($id));
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
                    new OA\Property(property: 'content', type: 'string', example: 'This is an updated post')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Post updated successfully'
            )
        ]
    )]

    public function updatePost(Request $request, $id) {
        $content = $request->input('content');
        return response()->json($this->postService->updatePost($id, $content));
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
        return response()->json($this->postService->deletePost($id));
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
                description: 'Posts retrieved successfully'
            )
        ]
    )]
    public function getAllPosts() {
        return response()->json($this->postService->getAllPosts());
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
                description: 'Posts retrieved successfully'
            )
        ]
    )]

    public function getPostsByUserId($id_user){
        return response()->json($this->postService->getPostsByUserId($id_user));
    }
}

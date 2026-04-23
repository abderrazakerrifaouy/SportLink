<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ConversationResource;
use App\Http\Resources\FollowResource;
use App\Http\Resources\MessageResource;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\UserResource;
use OpenApi\Attributes as OA;

class UserController extends Controller
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    #[OA\Get(
        path: "/users/{id}",
        summary: "Get a user by ID",
        description: "Retrieves a user by their ID",
        security: [["bearerAuth" => []]],
        tags: ["Users"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                description: "The user ID",
                required: true,
                schema: new OA\Schema(type: "string")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "User found",
                content: new OA\JsonContent(ref: "#/components/schemas/UserResource")
            ),
            new OA\Response(
                response: 404,
                description: "User not found"
            )
        ]
    )]
    // get user by id
    public function show($id)
    {
        $user = $this->userService->find((int) $id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        return response()->json(new UserResource($user));
    }

    #[OA\Put(
        path: "/users/{id}",
        summary: "Update a user",
        description: "Updates a user's information",
        security: [["bearerAuth" => []]],
        tags: ["Users"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                description: "The user ID",
                required: true,
                schema: new OA\Schema(type: "string")
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/UserRequest")
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "User updated successfully",
                content: new OA\JsonContent(ref: "#/components/schemas/UserResource")
            ),
            new OA\Response(
                response: 404,
                description: "User not found"
            )
        ]
    )]
    //update user
    public function update(Request $request, $id)
    {
        $data = $request->all();
        $user = $this->userService->update($id, $data);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        return response()->json(new UserResource($user));
    }

    #[OA\Delete(
        path: "/users/{id}",
        summary: "Delete a user",
        description: "Deletes a user by their ID",
        tags: ["Users"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                description: "The user ID",
                required: true,
                schema: new OA\Schema(type: "string")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "User deleted successfully"
            ),
            new OA\Response(
                response: 404,
                description: "User not found"
            )
        ]
    )]
    // delete user
    public function destroy($id)
    {
        $result = $this->userService->delete($id);
        if (!$result) {
            return response()->json(['error' => 'User not found'], 404);
        }
        return response()->json(['message' => 'User deleted successfully']);
    }

    #[OA\Get(
        path: "/users",
        summary: "Get all users",
        description: "Retrieves a list of all users",
        tags: ["Users"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Users found",
                content: new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(ref: "#/components/schemas/UserResource")
                )
            )
        ]
    )]
    // Get all users
    public function index()
    {
        $users = $this->userService->all();
        return response()->json(UserResource::collection($users));
    }

    #[OA\Get(
        path: "/users/search/{name}",
        summary: "Search users by name",
        description: "Searches for users by their name",
        tags: ["Users"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "name",
                in: "path",
                description: "The name to search for",
                required: true,
                schema: new OA\Schema(type: "string")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Users found",
                content: new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(ref: "#/components/schemas/UserResource")
                )
            )
        ]
    )]
    // Search users by name
    public function searchByName($name)
    {
        $users = $this->userService->searchByName($name);
        return response()->json(UserResource::collection($users));
    }

    #[OA\Get(
        path: "/users/search",
        summary: "Search users",
        description: "Searches for users by name using the q query parameter",
        tags: ["Users"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "q",
                in: "query",
                description: "Search keyword",
                required: false,
                schema: new OA\Schema(type: "string")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Users found",
                content: new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(ref: "#/components/schemas/UserResource")
                )
            )
        ]
    )]
    public function searchUsers(Request $request)
    {
        $query = $request->query('q', '');
        $users = $this->userService->searchByName($query);
        return response()->json(UserResource::collection($users));
    }


    // Messages MEthods


    #[OA\Post(
        path: "/users/{id}/message",
        summary: "Send a message to a user",
        description: "Sends a message to the specified user",
        tags: ["Messages"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                description: "The receiver's ID",
                required: true,
                schema: new OA\Schema(type: "string")
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "content", type: "string", example: "Hello, how are you?")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Message sent successfully"
            ),
            new OA\Response(
                response: 404,
                description: "User not found"
            )
        ]
    )]
    // send a message to another user
    public function sendMessage(Request $request, Int $receiverId)
    {

        $senderId = Auth::id();
        $content = $request->all()['content'];
        $message = $this->userService->SendMessage($senderId, $receiverId, $content);
        return response()->json($message, 201);
    }

    #[OA\Get(
        path: "/users/{userId1}/messages/{userId2}",
        summary: "Get messages between two users",
        description: "Retrieves the conversation between two users",
        security: [["bearerAuth" => []]],
        tags: ["Messages"],
        parameters: [
            new OA\Parameter(
                name: "userId1",
                in: "path",
                description: "The first user's ID",
                required: true,
                schema: new OA\Schema(type: "string")
            ),
            new OA\Parameter(
                name: "userId2",
                in: "path",
                description: "The second user's ID",
                required: true,
                schema: new OA\Schema(type: "string")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Messages found",
                content: new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(ref: "#/components/schemas/MessageResource")
                )
            ),
            new OA\Response(
                response: 404,
                description: "Conversation not found"
            )
        ]
    )]
    // get messages between two users
    public function getMessages($userId1, $userId2)
    {
        $conversation = $this->userService->getConversation($userId1, $userId2);
        return response()->json(MessageResource::collection($conversation));
    }

    #[OA\Get(
        path: "/users/conversations",
        summary: "Get conversations for a user",
        description: "Retrieves all conversations for the specified user",
        security: [["bearerAuth" => []]],
        tags: ["Messages"],

        responses: [
            new OA\Response(
                response: 200,
                description: "Conversations found",
                content: new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(ref: "#/components/schemas/ConversationResource")
                )
            ),
            new OA\Response(
                response: 404,
                description: "User not found"
            )
        ]
    )]

    public function getConversations()
    {
        $conversation = $this->userService->getConversations(Auth::id());
        return response()->json(ConversationResource::collection($conversation));
    }

    #[OA\Delete(
        path: "/users/messages/{messageId}",
        summary: "Delete a specific message",
        description: "Deletes a message by its ID",
        tags: ["Messages"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "messageId",
                in: "path",
                description: "The message's ID",
                required: true,
                schema: new OA\Schema(type: "string")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Message deleted successfully"
            ),
            new OA\Response(
                response: 404,
                description: "Message not found"
            )
        ]
    )]

    public function deleteMessage($messageId)
    {
        $result = $this->userService->deleteMessage($messageId);
        if (!$result) {
            return response()->json(['error' => 'Message not found'], 404);
        }
        return response()->json(['message' => 'Message deleted successfully']);
    }

    #[OA\Put(
        path: "/users/update/messages/{messageId}",
        summary: "Update a specific message",
        description: "Updates a message by its ID",
        tags: ["Messages"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "messageId",
                in: "path",
                description: "The message's ID",
                required: true,
                schema: new OA\Schema(type: "string")
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(
                        property: "content",
                        type: "string",
                        description: "The updated message content"
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Message updated successfully"
            ),
            new OA\Response(
                response: 404,
                description: "Message not found"
            )
        ]
    )]
    public function updateMessage(Request $request, $messageId)
    {
        $message = $request->input('content');
        $message = $this->userService->updateMessage($messageId, $message);
        if (!$message) {
            return response()->json(['error' => 'Message not found'], 404);
        }
        return response()->json($message);
    }


    // Follow/Unfollow MEthods



    #[OA\Post(
        path: "/users/{userId}/follow",
        summary: "Follow a user",
        description: "Follows a user by their ID",
        tags: ["Follows"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "userId",
                in: "path",
                description: "The user's ID",
                required: true,
                schema: new OA\Schema(type: "string")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "User followed successfully"
            ),
            new OA\Response(
                response: 404,
                description: "User not found"
            ),
            new OA\Response(
                response: 400,
                description: "Already following this user"
            )
        ]
    )]
    // Follow a user
    public function follow($id)
    {
        $followerId = Auth::id();
        $result = $this->userService->followUser($followerId, $id);
        if ($result === null) {
            return response()->json(['error' => 'Already following this user'], 400);
        }else if (!$result) {
            return response()->json(['error' => 'User not found'], 404);
        }
        return response()->json(['message' => 'Followed successfully']);
    }

    #[OA\Delete(
        path: "/users/{userId}/unfollow",
        summary: "Unfollow a user",
        description: "Unfollows a user by their ID",
        tags: ["Follows"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "userId",
                in: "path",
                description: "The user's ID",
                required: true,
                schema: new OA\Schema(type: "string")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "User unfollowed successfully"
            ),
            new OA\Response(
                response: 404,
                description: "User not found"
            ) ,
            new OA\Response(
                response: 400,
                description: "Already not following this user"
             )
        ]
    )]
    // Unfollow a user
    public function unfollow($id)
    {
        $followerId = Auth::id();
        $result = $this->userService->unfollowUser($followerId, $id);
        if ($result == null) {
            return response()->json(['error' => 'Already not following this user'], 400);
        }else if
        (!$result) {
            return response()->json(['error' => 'User not found'], 404);
        }
        return response()->json(['message' => 'Unfollowed successfully']);
    }

    #[OA\Get(
        path: "/users/{userId}/followers",
        summary: "Get user followers",
        description: "Retrieves a list of users who are following the specified user",
        tags: ["Follows"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "userId",
                in: "path",
                description: "The user's ID",
                required: true,
                schema: new OA\Schema(type: "string")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "List of followers retrieved successfully" ,
                content: new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(ref: "#/components/schemas/FollowResource")
                )
            ),
            new OA\Response(
                response: 404,
                description: "User not found"
            )
        ]
    )]
    public function followers($id)
    {
        $followers = $this->userService->getFollowers($id);
        return response()->json(FollowResource::collection($followers));
    }

    #[OA\Get(
        path: "/users/{userId}/following",
        summary: "Get user following",
        description: "Retrieves a list of users who are followed by the specified user",
        tags: ["Follows"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "userId",
                in: "path",
                description: "The user's ID",
                required: true,
                schema: new OA\Schema(type: "string")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "List of following retrieved successfully"
            ),
            new OA\Response(
                response: 404,
                description: "User not found"
            )
        ]
    )]
    public function following($id)
    {
        $following = $this->userService->getFollowing($id);
        return response()->json(FollowResource::collection($following));
    }

    #[OA\Get(
        path: "/users/{userId}/followers/count",
        summary: "Get follower count",
        description: "Retrieves the number of followers for the specified user",
        tags: ["Follows"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "userId",
                in: "path",
                description: "The user's ID",
                required: true,
                schema: new OA\Schema(type: "string")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Follower count retrieved successfully"
            ),
            new OA\Response(
                response: 404,
                description: "User not found"
            )
        ]
    )]
    public function countFollowers($id)
    {
        $count = $this->userService->countFollowers($id);
        return response()->json(['followers_count' => $count]);
    }

    #[OA\Get(
        path: "/users/{userId}/following/count",
        summary: "Get following count",
        description: "Retrieves the number of users following the specified user",
        tags: ["Follows"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "userId",
                in: "path",
                description: "The user's ID",
                required: true,
                schema: new OA\Schema(type: "string")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Following count retrieved successfully"
            ),
            new OA\Response(
                response: 404,
                description: "User not found"
            )
        ]
    )]
    public function countFollowing($id)
    {
        $count = $this->userService->countFollowing($id);
        return response()->json(['following_count' => $count]);
    }

    #[OA\Get(
        path: "/users/{userId}/is-following",
        summary: "Check if user is following another user",
        description: "Checks if the authenticated user is following the specified user",
        tags: ["Follows"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "userId",
                in: "path",
                description: "The user's ID",
                required: true,
                schema: new OA\Schema(type: "string")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Check result retrieved successfully"
            ),
            new OA\Response(
                response: 404,
                description: "User not found"
            )
        ]
    )]
    public function isFollowing($id)
    {
        $followerId = Auth::id();
        $followingId = $id;
        $isFollowing = $this->userService->isFollowing($followerId, $followingId);
        return response()->json(['is_following' => $isFollowing]);
    }
}

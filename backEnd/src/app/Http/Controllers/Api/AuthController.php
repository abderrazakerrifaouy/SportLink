<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Services\AuthService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

use function Symfony\Component\String\u;
use function Symfony\Component\Translation\t;

class AuthController extends Controller
{
    private $AuthService;

    public function __construct(AuthService $AuthService)
    {
        $this->AuthService = $AuthService;
    }

    #[OA\Post(
        path: "/register",
        summary: "Register a new user",
        description: "Creates a new user account",
        tags: ["Authentication"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/UserRequest")
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "User created successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "user", ref: "#/components/schemas/UserResource"),
                        new OA\Property(property: "token", type: "string", example: "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...")
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Validation error"
            )
        ]
    )]

    public function register(UserRequest $request)
    {
        $data = $request->validated();
        $result = $this->AuthService->register($data);
        return response()->json($result, 201);
    }

    #[OA\Post(
        path: "/login",
        summary: "Login a user",
        description: "Authenticates a user and returns a token",
        tags: ["Authentication"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "email", type: "string", format: "email", example: "john@example.com"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "password123")
                ]

            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Login successful",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "user", ref: "#/components/schemas/UserResource"),
                        new OA\Property(property: "token", type: "string", example: "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...")
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Unauthorized"
            )
        ]
    )]
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string|min:8',
        ]);
        $result = $this->AuthService->login($data['email'], $data['password']);
        if (!$result) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }
        return response()->json($result);
    }

    #[OA\Post(
        path: "/logout",
        summary: "Logout a user",
        security: [["sanctum" => []]],
        description: "Invalidates the user's token",
        tags: ["Authentication"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Logout successful"
            )
        ]
    )]
    public function logout()
    {
        $this->AuthService->logout(Auth::user());
        return response()->json(['message' => 'Logged out successfully']);
    }
}

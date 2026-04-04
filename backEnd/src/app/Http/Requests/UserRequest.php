<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    title: "User Request",
    description: "Request schema for creating or updating a user",
    type: "object",
    required: ["name", "email", "password"],
    properties: [
        new OA\Property(property: "name", type: "string", example: "John Doe"),
        new OA\Property(property: "email", type: "string", format: "email", example: "john@example.com"),
        new OA\Property(property: "password", type: "string", format: "password", example: "password123"),
        new OA\Property(property: "password_confirmation", type: "string", format: "password", example: "password123"),
        new OA\Property(property: "bio", type: "string", example: "This is John's bio"),
        new OA\Property(property: "profileImage", type: "string", format: "uri", example: "http://example.com/john.jpg"),
        new OA\Property(property: "bannerImage", type: "string", format: "uri", example: "http://example.com/john.jpg"),
        new OA\Property(property: "role", type: "string", enum: ["JOUEUR", "CLUB_ADMIN"], example: "JOUEUR"),
    ]
)]


class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'bio' => 'nullable|string',
            'profileImage' => 'nullable|url',
            'bannerImage' => 'nullable|url',
            'role' => 'nullable|string|in:JOUEUR,CLUB_ADMIN,Admin',
        ];
    }
}

<?php

namespace App\Services;

use App\Repositories\UserRepository;


class AuthService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register($data): array
    {
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        $user = $this->userRepository->create($data);
        if ($user->role === 'JOUEUR') {
            $user->joueur()->create([]);
        }
        $token = $user->createToken('auth_token')->plainTextToken;
        return ['user' => $user, 'token' => $token];
    }

    public function login($Email, $Password)
    {
        $user = $this->userRepository->findByEmail($Email);
        if (!$user || !password_verify($Password, $user->password)) {
            return null;
        }
        $token = $user->createToken('auth_token')->plainTextToken;
        return ['user' => $user, 'token' => $token];
    }

    public function logout($user)
    {
        $user->tokens()->delete();
        return true;
    }
}

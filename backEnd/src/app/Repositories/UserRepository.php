<?php

namespace App\Repositories;

use App\Models\Message;
use App\Models\User;

class UserRepository
{
    public function find(Int $id)
    {
        return User::find($id);
    }

    public function create($data)
    {
        return User::create($data);
    }

    public function update($id, $data)
    {
        $user = User::find($id);
        if ($user) {
            $user->update($data);
            return $user;
        }
        return null;
    }

    public function delete($id)
    {
        $user = User::find($id);
        if ($user) {
            $user->delete();
            return true;
        }
        return false;
    }

    public function all()
    {
        return User::all();
    }

    public function findByEmail($email)
    {
        return User::where('email', $email)->first();
    }

    public function findByUsername($username)
    {
        return User::where('username', $username)->first();
    }

    public function searchByName($name)
    {
        return User::where('name', 'LIKE', "%$name%")->get();
    }

}

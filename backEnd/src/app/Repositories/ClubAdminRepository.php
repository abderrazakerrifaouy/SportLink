<?php

namespace App\Repositories;

use App\Models\ClubAdmin;

class ClubAdminRepository {
    public function create($data) {
        return ClubAdmin::create($data);
    }

    public function findByUserId($userId) {
        return ClubAdmin::where('user_id', $userId)->first();
    }

    public function update($clubAdmin, $data) {
        $clubAdmin->update($data);
        return $clubAdmin;
    }

    public function delete($clubAdmin) {
        return $clubAdmin->delete();
    }

    public function all() {
        return ClubAdmin::all();
    }

    public function searcheByNane($name) {
        return ClubAdmin::where('nomClub', 'like', '%' . $name . '%')->get();
    }
}

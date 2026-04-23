<?php

namespace App\Repositories;

use App\Models\ClubAdmin;
use App\Models\ClubJoueurRequest;

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
        return ClubAdmin::with(['user.posts.user', 'user.posts.media', 'user', 'titres', 'joueurs.user'])->get();
    }

    public function searcheByNane($name) {
        return ClubAdmin::where('nomClub', 'like', '%' . $name . '%')->get();
    }

    public function findById($id) {
        return ClubAdmin::with(['user.posts.user', 'user.posts.media', 'user', 'titres', 'joueurs.user'])->find($id);
    }

    public function clupAdmineExists($userId) {
        return ClubAdmin::where('user_id', $userId)->exists();
    }

    public function findJoueurRequest($clubAdminId, $joueurId)
    {
        return ClubJoueurRequest::where('club_admin_id', $clubAdminId)
            ->where('joueur_id', $joueurId)
            ->first();
    }

    public function createJoueurRequest($clubAdminId, $joueurId)
    {
        return ClubJoueurRequest::create([
            'club_admin_id' => $clubAdminId,
            'joueur_id' => $joueurId,
            'status' => 'PENDING',
        ]);
    }
}

<?php

namespace App\Repositories;

use App\Models\Titre;


class TitreRepository {
    public function create($data) {
        return Titre::create($data);
    }

    public function find(Int $id) {
        return Titre::find($id);
    }

    public function update($titre, $data) {
        $titre->update($data);
        return $titre;
    }

    public function delete($titre) {
        return $titre->delete();
    }

    public function all() {
        return Titre::all();
    }

    public function getByClubAdminId($clubAdminId) {
        return Titre::where('club_admin_id', $clubAdminId)->get();
    }
}

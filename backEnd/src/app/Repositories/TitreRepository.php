<?php

namespace App\Repositories;

use App\Models\Titre;


class TitreRepository {
    public function create($data) {
        $incrementBy = max((int) ($data['number'] ?? 1), 1);

        $existingTitre = Titre::where('club_admin_id', $data['club_admin_id'])
            ->where('nomTitre', $data['nomTitre'])
            ->first();

        if ($existingTitre) {
            $existingTitre->increment('number', $incrementBy);

            if (!empty($data['description']) && $existingTitre->description !== $data['description']) {
                $existingTitre->description = $data['description'];
                $existingTitre->save();
            }

            return $existingTitre->fresh();
        }

        return Titre::create([
            'nomTitre' => $data['nomTitre'],
            'description' => $data['description'],
            'number' => $incrementBy,
            'club_admin_id' => $data['club_admin_id'],
        ]);
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

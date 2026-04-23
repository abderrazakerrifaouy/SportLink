<?php

namespace App\Repositories;


use App\Models\Experience;
use Illuminate\Database\Eloquent\Collection;


class ExperienceRepository
{
    public function create(array $data): Experience
    {
        return Experience::create($data);
    }
    public function all(): Collection
    {
        return Experience::all();
    }

    public function find(int $id): ?Experience
    {
        return Experience::find($id);
    }

    public function update(Experience $experience, array $data): Experience
    {
        $experience->update($data);
        return $experience;
    }

    public function delete(Experience $experience): void
    {
        $experience->delete();
    }

    public function findByJoueurId(int $joueurId): Collection
    {
        return Experience::with('clubAdmin')
            ->where('joueur_id', $joueurId)
            ->get();
    }

}

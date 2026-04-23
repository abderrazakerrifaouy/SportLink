<?php

namespace App\Repositories;

use App\Models\Joueur;
use Illuminate\Database\Eloquent\Collection;


class JoueurRepository
{
    public function create(array $data): Joueur
    {
        return Joueur::create($data);
    }
    public function all(): Collection
    {
        return Joueur::all();
    }

    public function find(int $id): ?Joueur
    {
        return Joueur::find($id);
    }

    public function allWithRelations(): Collection
    {
        return Joueur::with(['user', 'experiences.clubAdmin'])->get();
    }

    public function findWithRelations(int $id): ?Joueur
    {
        return Joueur::with(['user', 'experiences.clubAdmin'])->find($id);
    }

}

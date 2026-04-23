<?php

namespace App\Repositories;

use App\Models\ClubJoueurRequest;
use Illuminate\Database\Eloquent\Collection;

class ClubJoueurRequestRepository
{
    public function create(array $data): ClubJoueurRequest
    {
        return ClubJoueurRequest::create($data);
    }

    public function findById(int $id): ?ClubJoueurRequest
    {
        return ClubJoueurRequest::with(['joueur.user', 'club.user', 'experience.clubAdmin'])->find($id);
    }

    public function findByClubAdminId(int $clubAdminId): Collection
    {
        return ClubJoueurRequest::with(['joueur.user', 'club.user', 'experience.clubAdmin'])
            ->where('club_admin_id', $clubAdminId)
            ->latest()
            ->get();
    }

    public function findByJoueurId(int $joueurId): Collection
    {
        return ClubJoueurRequest::with(['joueur.user', 'club.user', 'experience.clubAdmin'])
            ->where('joueur_id', $joueurId)
            ->latest()
            ->get();
    }

    public function update(ClubJoueurRequest $request, array $data): ClubJoueurRequest
    {
        $request->update($data);
        return $request->refresh()->load(['joueur.user', 'club.user', 'experience.clubAdmin']);
    }
}

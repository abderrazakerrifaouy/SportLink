<?php

namespace App\Services;

use App\Models\ClubJoueurRequest;
use App\Repositories\ClubJoueurRequestRepository;
use App\Repositories\ExperienceRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ClubJoueurRequestService
{
    public function __construct(
        private ClubJoueurRequestRepository $requestRepository,
        private ExperienceRepository $experienceRepository,
    ) {
    }

    public function getPlayerHistory(int $joueurId): Collection
    {
        return $this->requestRepository->findByJoueurId($joueurId);
    }

    public function getClubAdminHistory(int $clubAdminId): Collection
    {
        return $this->requestRepository->findByClubAdminId($clubAdminId);
    }

    public function acceptRequest(int $requestId, int $joueurId): ClubJoueurRequest
    {
        $request = $this->requestRepository->findById($requestId);

        if (!$request) {
            throw new \DomainException('Request not found.');
        }

        if ((int) $request->joueur_id !== (int) $joueurId) {
            throw new \DomainException('You are not allowed to accept this request.');
        }

        if ($request->status === 'ACCEPTED' && $request->experience_id) {
            return $request;
        }

        if ($request->status === 'REJECTED') {
            throw new \DomainException('Rejected requests cannot be accepted.');
        }

        return DB::transaction(function () use ($request) {
            $clubAdmin = $request->club;

            $experience = $request->experience_id
                ? $request->experience
                : $this->experienceRepository->create([
                    'idClub' => $request->club_admin_id,
                    'image' => $clubAdmin?->user?->profileImage,
                    'joinDate' => now()->toDateString(),
                    'endDate' => null,
                    'place' => $clubAdmin?->ecole ?? $clubAdmin?->nomClub ?? 'Unknown',
                    'categoryType' => 'SENIOR',
                    'joueur_id' => $request->joueur_id,
                ]);

            return $this->requestRepository->update($request, [
                'status' => 'ACCEPTED',
                'experience_id' => $experience->id,
            ]);
        });
    }

    public function rejectRequest(int $requestId, int $joueurId): ClubJoueurRequest
    {
        $request = $this->requestRepository->findById($requestId);

        if (!$request) {
            throw new \DomainException('Request not found.');
        }

        if ((int) $request->joueur_id !== (int) $joueurId) {
            throw new \DomainException('You are not allowed to reject this request.');
        }

        if ($request->status === 'ACCEPTED') {
            throw new \DomainException('Accepted requests cannot be rejected.');
        }

        return $this->requestRepository->update($request, [
            'status' => 'REJECTED',
        ]);
    }
}

<?php

namespace App\Services;

use App\Repositories\ClubAdminRepository;


class ClubAdminService{
    private $clubAdminRepository;

    public function __construct(ClubAdminRepository $clubAdminRepository ) {
        $this->clubAdminRepository = $clubAdminRepository;
    }

    public function createClubAdmin( $clubAdminData) {
        if (isset($clubAdminData['user_id'])) {
            $existingClubAdmin = $this->clubAdminRepository->findByUserId($clubAdminData['user_id']);
            if ($existingClubAdmin) {
                throw new \DomainException('This user is already assigned to a Club Admin.');
            }
        }
        return $this->clubAdminRepository->create($clubAdminData);
    }

    public function getClubAdminByUserId($userId) {
        return $this->clubAdminRepository->findByUserId($userId);
    }

    public function updateClubAdmin($clubAdmin, $data) {
        return $this->clubAdminRepository->update($clubAdmin, $data);
    }

    public function deleteClubAdmin($clubAdmin) {
        return $this->clubAdminRepository->delete($clubAdmin);
    }

    public function AllClubAdmins() {
        return $this->clubAdminRepository->all();
    }

    public function searchClubAdminsByName($name) {
        return $this->clubAdminRepository->searcheByNane($name);
    }

    public function getClubAdminById($id) {
        return $this->clubAdminRepository->findById($id);
    }

    public function clubAdminExists($userId) {
        return $this->clubAdminRepository->clupAdmineExists($userId);
    }

    public function inviteJoueurToClub($userId, $joueurId)
    {
        $clubAdmin = $this->clubAdminRepository->findByUserId($userId);

        if (!$clubAdmin) {
            throw new \DomainException('Club Admin profile not found for this user.');
        }

        if ($clubAdmin->joueurs()->where('joueurs.id', $joueurId)->exists()) {
            throw new \DomainException('This player is already in your club.');
        }

        $existingRequest = $this->clubAdminRepository->findPendingJoueurRequest($clubAdmin->id, $joueurId);

        if ($existingRequest) {
            throw new \DomainException('An invitation request is already pending for this player.');
        }

        return $this->clubAdminRepository->createJoueurRequest($clubAdmin->id, $joueurId);
    }

}

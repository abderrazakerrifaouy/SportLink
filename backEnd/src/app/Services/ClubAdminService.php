<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\ClubAdminRepository;
use App\Repositories\UserRepository;


class ClubAdminService{
    private $clubAdminRepository;

    public function __construct(ClubAdminRepository $clubAdminRepository ) {
        $this->clubAdminRepository = $clubAdminRepository;
    }

    public function createClubAdmin( $clubAdminData) {
        if (isset($clubAdminData['user_id'])) {
            $existingClubAdmin = $this->clubAdminRepository->findByUserId($clubAdminData['user_id']);
            if ($existingClubAdmin) {
                throw new \Exception('A club admin with this user ID already exists.');
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

}

<?php

namespace App\Services;

use App\Repositories\TitreRepository;


class TitreService {
    protected $titreRepository;

    public function __construct(TitreRepository $titreRepository) {
        $this->titreRepository = $titreRepository;
    }

    public function create($data) {
        return $this->titreRepository->create($data);
    }

    public function find(Int $id) {
        return $this->titreRepository->find($id);
    }

    public function update($id, $data) {
        $titre = $this->find($id);
        if ($titre) {
            return $this->titreRepository->update($titre, $data);
        }
        return null;
    }

    public function delete($id) {
        $titre = $this->find($id);
        if ($titre) {
            return $this->titreRepository->delete($titre);
        }
        return false;
    }

    public function all() {
        return $this->titreRepository->all();
    }

    public function getByClubAdminId($clubAdminId) {
        return $this->titreRepository->getByClubAdminId($clubAdminId);
    }
}

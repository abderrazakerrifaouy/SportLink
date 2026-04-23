<?php

namespace App\Services;

use App\Repositories\JoueurRepository;
use App\Repositories\ExperienceRepository;


class JoueurService
{
    protected $joueurRepository;
    protected $experienceRepository;

    public function __construct(JoueurRepository $joueurRepository, ExperienceRepository $experienceRepository)
    {
        $this->joueurRepository = $joueurRepository;
        $this->experienceRepository = $experienceRepository;
    }

    public function getAllJoueurs()
    {
        return $this->joueurRepository->allWithRelations();
    }

    public function getJoueurById(int $id)
    {
        return $this->joueurRepository->findWithRelations($id);
    }

    public function getExperiencesByJoueurId(int $joueurId)
    {
        return $this->experienceRepository->findByJoueurId($joueurId);
    }
    public function createJoueur(array $data)
    {
        return $this->joueurRepository->create($data);
    }

    public function createExperience(array $data)
    {
        return $this->experienceRepository->create($data);
    }

    public function updateExperience(int $id, array $data)
    {
        $experience = $this->experienceRepository->find($id);
        if ($experience) {
            return $this->experienceRepository->update($experience, $data);
        }
        return null;
    }

    public function deleteExperience(int $id)
    {
        $experience = $this->experienceRepository->find($id);
        if ($experience) {
            $this->experienceRepository->delete($experience);
            return true;
        }
        return false;
    }
}

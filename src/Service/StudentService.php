<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class StudentService
{

    private $em;

    public function __construct(
        EntityManagerInterface $em,
        AuthService $authService
    )
    {
        $this->em = $em;
        $this->authService = $authService;
    }


    /**
     * Obtenir tous les étudiants en fonction du campus du cordinateur pégado
     * @param int $promotion
     * @return void
     */
    public function getAllStudentsPerCampus(int $promotion = 0, $filterCampus = null, $researchBar = ""){

        // Obtenir tous les étudiants en fonction du campus et de la classe sélectionnée
        $allStudents = $this->em->getRepository(User::class)->getAllStudentsPerPromotion($promotion, $filterCampus, $researchBar);

        return $allStudents;


    }


}
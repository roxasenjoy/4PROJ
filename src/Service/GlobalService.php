<?php

namespace App\Service;

use App\Entity\UserGrade;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;

class GlobalService
{

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Obtenir la date actuelle
     * @return \DateTime
     */
    public function getTodayDate(): \DateTime
    {
        $date = new \DateTime();
        $date->setTimezone(new DateTimeZone('Europe/Paris'));
        $date->getTimestamp();

        return $date;
    }

    /**
     * Obtenir toutes les notes d'un utilisateur
     * @param $user
     * @return mixed
     */
    public function getNotes($user){
        return $this->em->getRepository(UserGrade::class)->getGradesByUser($user->getId());
    }


}
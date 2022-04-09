<?php

namespace App\Service;

use App\Entity\Subject;
use App\Entity\SubjectDate;
use App\Entity\UserComptability;
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

    public function getAgenda($user){

        // On récupère les informations concernant l'utilisateur ->
        $agenda = $this->em->getRepository(SubjectDate::class)->getAgendaByUser($user->getId());

        // On reformate les informations pour que ça colle avec le design du Dashboard
        $newAgenda = [];
        foreach($agenda as $key=>$value){
            $newAgenda[] = array(
                'name' => $value['name'],
                'date' => date_format($value['date_begin'],"m/d"),
                'date_begin' => date_format($value['date_begin'],"H:i"),
                'date_end' => date_format($value['date_end'],"H:i")
            );
        }

        return $newAgenda;
    }

    /**
     * Obtenir tous les cours de l'étudiant - En fonction de son année scolaire
     * @param $user
     * @return mixed
     */
    public function getCours($user){
        return $this->em->getRepository(Subject::class)->getAllLessonsByLevel($user);
    }


    /**
     * Obtenir le résultat total de la comptabilité de l'étudiant
     * @param $userId
     * @return mixed
     */
    public function getUserTotalComptability($userId){

        //Récupération de la somme global
        $total = $this->em->getRepository(UserComptability::class)->getTotalComptabilityPerUser($userId);

        // On calcule avec les deux éléments
        $total = $total[0][1] + $total[0][2];

        return $total;
    }


}
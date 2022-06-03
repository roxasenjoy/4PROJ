<?php

namespace App\Service;

use App\Entity\Offer;
use App\Entity\Subject;
use App\Entity\SubjectDate;
use App\Entity\UserComptability;
use App\Entity\UserGrade;
use App\Repository\NotificationRepository;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

class GlobalService
{

    private $em;
    private NotificationRepository $notification;


    public function __construct(EntityManagerInterface $em, PaginatorInterface $paginator)
    {
        $this->em = $em;
        $this->paginator = $paginator;
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


    /**
     * Obtenir le nombre total de crédits ECTS de l'étudiant.
     * Si aucune valeur de base : Un étudiant gagnera 60 crédits par année validés
     * @param $userId
     * @return float|int
     */
    public function getAllEcts($user){

        if($user->getRole()->getId() === 12){
            $totalECTS = 0;
            // Obtenir le niveau actuel de l'étudiant
            if($user->getUserExtended()){
                $actualYear = $user->getUserExtended()->getActualLevel()->getYear();
                $hasPreviousYear = $user->getUserExtended()->getPreviousLevel()->getYear();

                // Si l'étudiant ne possède pas de previous_level_id, on multiplie son année actuel -1 par 60
                if($hasPreviousYear === null){
                    $totalECTS = (($actualYear -1) * 60);
                }
            }

            // Ajouter les crédits actuel de l'année en cours
            return $totalECTS + intval($this->em->getRepository(UserGrade::class)->getTotalEctsPerUser($user->getId())[0]['1']);
        }

        return 0;


    }

    public function getAllOffer(){
        return $this->em->getRepository(Offer::class)->countOffer()[0]['1'];
    }

    /**
     * @param $max - La note maximal disponible.
     * @return array
     */
    public function generatePoints($max)
    {
        $min        = 1;
        $values     = array();

        for($min; $min <= $max; $min++){
            $values[$min] = $min;
        }

        return $values;
    }

    public function generatePassword() {
        $alphabet       = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass           = array(); //remember to declare $pass as an array
        $alphaLength    = strlen($alphabet) - 1; //put the length -1 in cache

        for ($i = 0; $i < 8; $i++) {
            $n      = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }

        return implode($pass); //turn the array into a string
    }


    /**
     * Création d'une pagination en fonction de la query et tu nombre d'éléments par page
     * @param $query
     * @param $maxPerPage
     * @param $request Request
     * @return PaginationInterface
     */
    public function generatePagination($query, $maxPerPage, Request $request): PaginationInterface
    {
        return $this->paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $maxPerPage /* limit */
        );
    }


}
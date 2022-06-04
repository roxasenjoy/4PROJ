<?php

namespace App\Service;

use App\Entity\Campus;
use App\Entity\StudyLevel;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;

class ImportService
{

    public function __construct(
        EntityManagerInterface $em
    )

    {
        $this->em = $em;
    }


    /**
     * Permet de récupérer l'id du campus ou de le créer si celui-ci n'existe pas
     */
    public function getCampusEntity($campusString): ?int
    {

        // S'il n'y a pas de campus, on met le nom None pour déterminer que cet utilisateur n'a pas de campus
        if(!$campusString){
            $campusString = 'AUCUN';
        }

        // Si le campus existe, on récupère toutes les informations
        $isCampusExist = $this->em->getRepository(Campus::class)->findBy(array('name' => strtoupper($campusString)));

        // Le campus est présent, on récupère son id en fonction du nom en MAJUSCULE
        if($isCampusExist) {
            return $isCampusExist[0]->getId();
        }

        // Le campus n'existe pas, on le créer et on récupère son ID
        $newCampus = new Campus();
        $newCampus->setName(strtoupper($campusString));

        $this->em->persist($newCampus);
        $this->em->flush();

        return $newCampus->getId();
    }

    /**
     * Transforme la date présente dans l'excel dans un format DATETIME
     * @throws \Exception
     */
    public function setDateIntoDatetime($date): DateTime|bool
    {

        if($date){
            $date = DateTime::createFromFormat('d/m/Y', $date);
        } else {
            $date = new DateTime();
        }

        return $date;

    }

    /**
     * Détermine l'année de sortie en fonction de l'année qui passe actuellement
     * @param $data
     * @return int (Return l'année)
     */
    public function getYearExit($data): ?int
    {
        // On récupère l'année en cours (id)
        $yearRemaining = match ($data['cursus']) {
            'B.Eng1' => 5,
            'B.Eng2' => 4,
            'B.Eng3' => 3,
            'M.Eng1' => 2,
            'M.Eng2' => 1,
            default => 5
        };

        //On récupère l'année actuelle
        $now = intval(date("Y"));

        // On ajoute le temps restant à l'année actuelle
        return $now + $yearRemaining;
    }

    public function getYearEntry($data){
        // On récupère l'année en cours (id)
        $yearRemaining = match ($data['cursus']) {
            'B.Eng1' => 1,
            'B.Eng2' => 2,
            'B.Eng3' => 3,
            'M.Eng1' => 4,
            'M.Eng2' => 5,
            default => 1
        };

        //On récupère l'année actuelle
        $now = intval(date("Y"));

        // On ajoute le temps restant à l'année actuelle
        return $now + $yearRemaining;
    }

    /**
     * Modifie les termes utilisés dans l'excel par les level de la base de données
     * @param $levelName
     */
    public function getActualLevel($data)
    {
        $year = match ($data['cursus']) {
            'B.Eng1' => 1,
            'B.Eng2' => 2,
            'B.Eng3' => 3,
            'M.Eng1' => 4,
            'M.Eng2' => 5,
            default => 1
        };

        // Récupération de l'id dans la table des niveaux scolaires
        return $this->em->getRepository(StudyLevel::class)->findBy(array('year' => $year))[0]->getId();

    }

    /**
     * Permet de définir le niveau précédent de l'utilisateur (Bac, licence etc..)
     * @param string $previousLevel
     * @return int
     */
    public function getPreviousLevel($data): int
    {
        return 'BAC';
    }

    public function defineYear($level): int
    {
        return match ($level) {
            'BAC' => 1,
            'CAP', 'BEP', 'DUT', 'LICENCE' => 2,
            'BUT' => 3,
            'MASTER' => 4,
            default => 1
        };

    }
}
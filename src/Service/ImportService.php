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
            $campusString = 'A ATTRIBUER';
        }

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
     * Détermine l'année de sortie si l'utilisateur est encore étudiant
     * @param $data
     * @return int|null
     */
    public function getExitLevel($data): ?int
    {
        // Année d'entrée + le temps restant = Année de sortie
        $yearExit = intval($data['year_of_entry']);

        if(filter_var($data['still_student'], FILTER_VALIDATE_BOOLEAN)){ // L'utilisateur est toujours étudiant
            $yearExit += match ($data['level']) {
                'B.ENG 1' => 5,
                'B.ENG 2' => 4,
                'B.ENG 3' => 3,
                'M.ENG 1' => 2,
                'M.ENG 2' => 1,
                default => 0,
            };
            return $yearExit;
        }

        return $yearExit;
    }

    /**
     * Modifie les termes utilisés dans l'excel par les thèmes de la base de données
     * @param $levelName
     */
    public function getLevelId($data)
    {
        $year = match ($data['level']) {
            'B.ENG 1' => 1,
            'B.ENG 2' => 2,
            'B.ENG 3' => 3,
            'M.ENG 1' => 4,
            'M.ENG 2' => 5,
            default => 1
        };

        if(!$data['level']){
            $year = match ($data['level_of_exit']) {
                'B.ENG 1' => 1,
                'B.ENG 2' => 2,
                'B.ENG 3' => 3,
                'M.ENG 1' => 4,
                'M.ENG 2' => 5,
                default => 1
            };
        }


        return $this->em->getRepository(StudyLevel::class)->findBy(array('year' => $year))[0];

    }

    /**
     * Permet de définir le niveau précédent de l'utilisateur (Bac, licence etc..)
     * @param string $previousLevel
     * @return int
     */
    public function getPreviousLevel(string $previousLevel): int
    {
        // S'il n'y a pas de campus, on met le nom None pour déterminer que cet utilisateur n'a pas de campus
        if(!$previousLevel){
            $previousLevel = 'Inconnu';
        }

        $previousLevel = strtoupper($previousLevel);

        $isLevelExist = $this->em->getRepository(StudyLevel::class)->findBy(array('name' => $previousLevel));

        // Le campus est présent, on récupère son id en fonction du nom en MAJUSCULE
        if($isLevelExist) {
            return $isLevelExist[0]->getId();
        }

        // Le campus n'existe pas, on le créer et on récupère son ID
        $getYear    = $this->defineYear($previousLevel);
        $newLevel   = new StudyLevel();
        $newLevel   ->setName($previousLevel)
                    ->setYear($getYear);

        $this->em->persist($newLevel);
        $this->em->flush();

        return $newLevel->getId();
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
<?php

namespace App\Service;

use App\Entity\SubjectDate;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class UserService
{

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;

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


}
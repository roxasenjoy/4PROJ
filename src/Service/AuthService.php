<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class AuthService
{

    private $em;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        // Avoid calling getUser() in the constructor: auth may not
        // be complete yet. Instead, store the entire Security object.
        $this->security = $security;

    }

    /**
     * Vérification de l'état de l'utilisateur. Est ce qu'il est connecté ?
     * Si oui, on return les informations de l'utilisateur
     * Si non, on return false car il n'est pas connecté
     */
    public function isAuthenticatedUser()
    {
        $user = $this->security->getUser();

        if($user){
            return $user;
        }

        return false;
    }


}
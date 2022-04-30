<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Security;

class RegexService
{

    private $em;

    public function __construct(
        EntityManagerInterface $em,
        Security $security,
        RouterInterface $router)
    {
        $this->em = $em;
        $this->security = $security;
        $this->router = $router;

    }

    public function stringValidation($string){

        if(preg_match("/^[a-zA-Z]+$/", $string) == 1) {
            return true;
        }

        return false;

    }



}
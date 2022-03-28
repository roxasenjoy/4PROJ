<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/', name: 'app_homepage')]
    public function index(): Response
    {

        $user = $this->getUser();

        return $this->render('base.html.twig', [
            'controller_name' => "JE SUIS UN TEXTE LIE A CETTE VARIABLE POUR AFFICHER SUR LE FRONT",
            'user' => $user
        ]);
    }
}

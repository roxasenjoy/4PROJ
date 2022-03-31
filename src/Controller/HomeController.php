<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\AuthService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

    public function __construct(
        EntityManagerInterface $em,
        AuthService $authService,
    )
    {
        $this->em = $em;
        $this->authService = $authService;
    }

    #[Route('/', name: 'app_homepage')]
    public function index(): Response
    {

        // Si un utilisateur n'est pas connecté on le ramène vers la login page
        if ( !$this->authService->isAuthenticatedUser() ) {
            return $this->redirectToRoute('app_login');
        }

        $user = $this->getUser();


        return $this->render('base.html.twig', [
            'controller_name' => "JE SUIS UN TEXTE LIE A CETTE VARIABLE POUR AFFICHER SUR LE FRONT",
            'user' => $user
        ]);
    }
}

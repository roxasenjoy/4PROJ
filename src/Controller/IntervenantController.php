<?php

namespace App\Controller;

use App\Entity\Intervenant;
use App\Entity\Subject;
use App\Entity\User;
use App\Service\AuthService;
use App\Service\GlobalService;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IntervenantController extends AbstractController
{

    private AuthService $authService;
    private EntityManagerInterface $em;

    public function __construct(
        EntityManagerInterface $em,
        AuthService $authService
    )
    {
        $this->em = $em;
        $this->authService = $authService;
    }

    #[Route('/intervenant', name: 'app_intervenant')]
    public function getIntervenants(): Response
    {
        $user = $this->authService->isAuthenticatedUser();

        $intervenants = $this->em->getRepository(Intervenant::class)->getIntervenants($user);

        return $this->render('intervenant/intervenant.html.twig', [
            'intervenants' => $intervenants
        ]);
    }
}

<?php

namespace App\Controller;

use App\Entity\Role;
use App\Entity\Subject;
use App\Entity\SubjectDate;
use App\Entity\User;
use App\Entity\UserComptability;
use App\Service\AuthService;
use App\Service\GlobalService;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ComptabilityController extends AbstractController
{
    private AuthService $authService;
    private EntityManagerInterface $em;

    public function __construct(
        EntityManagerInterface $em,
        AuthService $authService,
        GlobalService $globalService
    )

    {
        $this->em = $em;
        $this->authService = $authService;
        $this->globalService = $globalService;
    }

    #[Route('/user/comptability', name: 'app_user_comptability')]
    public function index(): Response
    {
        $user = $this->authService->isAuthenticatedUser();

        // Récupération de la comptabilité de l'étudiant
        $comptability = $this->em->getRepository(UserComptability::class)->getComptabilityPerUser($user->getId());

        $total = $this->globalService->getUserTotalComptability($user->getId());

        if(!$total){
            $total = 0;
        }

        return $this->render('comptability/index.html.twig', [
            'comptability'  => $comptability,
            'total'         => $total
        ]);
    }
}

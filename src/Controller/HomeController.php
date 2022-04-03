<?php

namespace App\Controller;

use App\Entity\StudyLevel;
use App\Entity\Subject;
use App\Entity\SubjectDate;
use App\Entity\User;
use App\Entity\UserExtended;
use App\Entity\UserGrade;
use App\Entity\UserSubject;
use App\Service\AuthService;
use App\Service\UserService;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class HomeController extends AbstractController
{

    private AuthService $authService;
    private EntityManagerInterface $em;

    public function __construct(
        EntityManagerInterface $em,
        AuthService $authService,
        UserService $userService
    )
    {
        $this->em = $em;
        $this->authService = $authService;
        $this->userService = $userService;
    }

    #[Route('/', name: 'app_homepage')]
    public function index(): Response
    {

        // Si un utilisateur n'est pas connecté on le ramène vers la login page
        if ( !$this->authService->isAuthenticatedUser() ) {
            return $this->redirectToRoute('app_login');
        }

        $user = $this->authService->isAuthenticatedUser();

        return $this->render('dashboard/dashboard.html.twig', [
            'user' => $user, // Utilisateur
            'userGrades' => $this->getNotes($user), // Dernières évaluations
            'agenda' => $this->userService->getAgenda($user) // Agenda
        ]);
    }

    public function getNotes($user){
        return $this->em->getRepository(UserGrade::class)->getGradesByUser($user->getId());
    }


}

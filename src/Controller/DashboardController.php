<?php

namespace App\Controller;

use App\Entity\Subject;
use App\Entity\User;
use App\Repository\NotificationRepository;
use App\Service\AuthService;
use App\Service\GlobalService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
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

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/', name: 'app_dashboard')]
    public function index(NotificationRepository $notification, Request $request): Response
    {


        $user = $this->authService->isAuthenticatedUser();

        if($user->getRoles()[0] == 'ROLE_TEACHER'){
            return $this->redirectToRoute('app_cours_campus_teacher');
        }

        $notifications = $notification->getNotifications($user);

        return $this->render('dashboard/dashboard.html.twig', [
            'userGrades'    => $this->globalService->getNotes($user), // Dernières évaluations
            'agenda'        => $this->globalService->getAgenda($user), // Agenda
            'cours'         => $this->globalService->getCours($user), // Cours
            'comptability'  => $this->globalService->getUserTotalComptability($user->getId()), // La comptabilité de l'étudiant
            'ectsTotal'     => $this->globalService->getAllEcts($user), // Total des crédits ECTS de l'étudiants
            'offersTotal'   => $this->globalService->getAllOffer(),
            'allStudents'   => $this->em->getRepository(User::class)->getAllStudentsPerPromotion(0, null),
            'allLessons'    => $this->em->getRepository(Subject::class)->getAllLessons(0),
            'totalStudent'  => $this->em->getRepository(User::class)->countStudent($user)[0]['totalStudent'],
            'totalLesson'   => $this->em->getRepository(Subject::class)->countLesson()[0]['totalLesson'],
            'notifications' => $this->globalService->generatePagination($notifications, 5, $request)
        ]);
    }
}

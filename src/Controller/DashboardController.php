<?php

namespace App\Controller;

use App\Entity\Role;
use App\Entity\StudyLevel;
use App\Entity\User;
use App\Entity\UserExtended;
use App\Service\AuthService;
use App\Service\GlobalService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\Transport\TransportInterface;

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
    public function index(): Response
    {
        $user = $this->authService->isAuthenticatedUser();

        return $this->render('dashboard/dashboard.html.twig', [
            'userGrades'    => $this->globalService->getNotes($user), // Dernières évaluations
            'agenda'        => $this->globalService->getAgenda($user), // Agenda
            'cours'         => $this->globalService->getCours($user), // Cours
            'comptability'  => $this->globalService->getUserTotalComptability($user->getId()), // La comptabilité de l'étudiant
            'ectsTotal'     => $this->globalService->getAllEcts($user), // Total des crédits ECTS de l'étudiants
            'offersTotal'   => $this->globalService->getAllOffer()
        ]);
    }
}

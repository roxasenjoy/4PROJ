<?php

namespace App\Controller;

use App\Entity\StudyLevel;
use App\Entity\User;
use App\Entity\UserExtended;
use App\Service\AuthService;
use App\Service\GlobalService;
use App\Service\UserService;
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
        UserService $userService,
        GlobalService $globalService
    )
    {
        $this->em = $em;
        $this->authService = $authService;
        $this->userService = $userService;
        $this->globalService = $globalService;
    }

    #[Route('/', name: 'app_dashboard')]
    public function index(): Response
    {

        $user = $this->authService->isAuthenticatedUser();

//        $test = new UserExtended();
//
//        $test->setUser($this->em->getRepository(User::class)->find(34))
//            ->setActualLevel($this->em->getRepository(StudyLevel::class)->find(3))
//            ->setPreviousLevel($this->em->getRepository(StudyLevel::class)->find(2))
//            ->setBirthday($this->globalService->getTodayDate())
//            ->setAddress('Je suis une adresse')
//            ->setGender(0)
//            ->setRegion('REGION')
//            ->setYearEntry('2020')
//            ->setYearExit('2025')
//            ->setNbAbscence(0)
//            ->setIsStudent(true)
//            ->setIsHired(true)
//            ->setHasProContract(false);
//
//        $this->em->persist($test);
//        $this->em->flush();

        return $this->render('dashboard/dashboard.html.twig', [
            'userGrades' => $this->globalService->getNotes($user), // Dernières évaluations
            'agenda' => $this->userService->getAgenda($user) // Agenda
        ]);
    }
}

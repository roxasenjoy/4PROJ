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

    #[Route('/intervenant', name: 'app_intervenant')]
    public function getIntervenants(): Response
    {


//        $teacher = new Intervenant();
//        $teacher->setUser($this->em->getRepository(User::class)->find(37))
//            ->setSubject($this->em->getRepository(Subject::class)->find(1));
//
//        $this->em->persist($teacher);
//        $this->em->flush();

        $user = $this->authService->isAuthenticatedUser();

        $intervenants = $this->em->getRepository(Intervenant::class)->getIntervenants($user);

        return $this->render('intervenant/intervenant.html.twig', [
            'intervenants' => $intervenants
        ]);
    }
}

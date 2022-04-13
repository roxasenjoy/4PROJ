<?php

namespace App\Controller;

use App\Entity\Intervenant;
use App\Entity\Subject;
use App\Service\AuthService;
use App\Service\GlobalService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CoursController extends AbstractController
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

    #[Route('/cours', name: 'app_cours')]
    public function getCours(): Response
    {
        $user = $this->authService->isAuthenticatedUser();

        return $this->render('cours/cours.html.twig', [
            'cours' => $this->globalService->getCours($user),
        ]);
    }

    /* {id} = On passe une variable dynamique, par exemple les id changent en fonction du cours cliqué */
    #[Route('/cours/details/{id}', name: 'app_cours_details')]
    public function getDetails(Request $request): Response
    {

        // Récuperer l'id du cours en question
        $cours = $this->em->getRepository(Subject::class)->find($request->get('id'));

        return $this->render('cours/filter.html.twig', [
            'cours' => $cours
        ]);
    }
}

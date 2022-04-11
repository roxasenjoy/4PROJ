<?php

namespace App\Controller;

use App\Entity\Intervenant;
use App\Service\AuthService;
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
        $intervenants = null;

        if($user->getUserExtended()){
            $intervenants = $this->em->getRepository(Intervenant::class)->getIntervenants($user);
        }


        return $this->render('intervenant/intervenant.html.twig', [
            'intervenants' => $intervenants
        ]);
    }
}

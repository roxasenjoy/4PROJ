<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class ApiController extends AbstractController
{
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function __invoke()
    {
        $user = $this->security->getUser();
        return $user;
    }

    /**
     * Gestion de l'auth à l'API
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */

    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function api_login()
    {

        $user = $this->getUser();

        if($user === null){
            return $this->json([
                'message' => 'Missing Credentials'
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $this->json([
            'username' => $user->getUsername(),
            'roles' => $user->getRoles()
        ]);
    }

    #[Route('/api/logout', name: 'api_logout', methods: ['POST'])]
    public function logout(){

    }

    /**
     * Récupération des données
     */
    #[Route('v1/user', name: 'api_get_user', methods: ['POST'])]
    public function apiUserConnected(){

        $user = $this->security->getUser();

        return $user;

    }

}

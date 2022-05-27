<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{

    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function api_login()
    {

        $user = $this->getUser();

//        if($user === null){
//            return $this->json([
//                'message' => 'Missing Credentials'
//            ], Response::HTTP_UNAUTHORIZED);
//        }
//
//        $token = $this->get('lexik_jwt_authentication.encoder')
//            ->encode([
//                'username' => $user->getUsername(),
//                'exp' => time() + 3600 // 1 hour expiration
//            ]);

        return $this->json([
            'username' => $user->getUsername(),
            'roles' => $user->getRoles()
        ]);
    }

    #[Route('/api/logout', name: 'api_logout', methods: ['POST'])]
    public function logout(){

    }
}

<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Security;

class AuthService
{

    private $em;

    public function __construct(
        EntityManagerInterface $em,
        Security $security,
        RouterInterface $router,
        JWTEncoderInterface $jwtEncoder)
    {
        $this->em = $em;
        $this->security = $security;
        $this->router = $router;
        $this->jwtEncoder = $jwtEncoder;

    }

    /**
     * Vérification de l'état de l'utilisateur. Est ce qu'il est connecté ?
     * Si oui, on return les informations de l'utilisateur
     * Si non, on return false car il n'est pas connecté
     */
    public function isAuthenticatedUser()
    {
        $user = $this->security->getUser();

        if($user){
            return $user;
        }

        return false;
    }

    /**
     * Génération du Token JWT pour l'utilisateur
     * @param User $user
     * @return mixed
     */
    public function generateToken(User $user){
        $token = $this->jwtEncoder->encode(
            [
                'username' => $user->getUsername(),
                'password' => $user->getPassword(),
                // token expire after a month
                'exp' => time() + 2592000,
            ]
        );

        return $token;
    }



}
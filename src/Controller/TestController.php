<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\Defense;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{

    #[Route('/', name: 'app_test_controller_php')]
    public function index(): Response
    {
        return $this->render('test_controller_php/index.html.twig', [
            'controller_name' => "JE SUIS UN TEXTE LIE A CETTE VARIABLE POUR AFFICHER SUR LE FRONT",
        ]);
    }
}

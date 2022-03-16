<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{

    #[Route('/', name: 'app_test_controller_php')]
    public function index(): Response
    {
        return $this->render('test_controller_php/index.html.twig', [
            'controller_name' => 'Bienvenue à vous !',
        ]);
    }
}

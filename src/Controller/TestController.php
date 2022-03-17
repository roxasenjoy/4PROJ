<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\Defense;
use App\Entity\User;
use App\Entity\UserGrade;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        
    }

    #[Route('/', name: 'app_test_controller_php')]
    public function index(): Response
    {



        
    
        return $this->render('test_controller_php/index.html.twig', [
            'controller_name' => "JE SUIS UN TEXTE LIE A CETTE VARIABLE POUR AFFICHER SUR LE FRONT",
        ]);
    }
}

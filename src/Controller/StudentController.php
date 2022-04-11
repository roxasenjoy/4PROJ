<?php

namespace App\Controller;

use App\Service\StudentService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

#[Route('/pedago')]
class StudentController extends AbstractController
{
    public function __construct(StudentService $studentService, EntityManagerInterface $em,){

        $this->em = $em;
        $this->studentService = $studentService;

    }

    #[Route('/student', name: 'app_student')]
    public function index(): Response
    {

        $allStudents = $this->studentService->getAllStudentsPerCampus();

        return $this->render('student/student.html.twig', [
            'allStudents' => $allStudents
        ]);
    }

    #[Route('/student/{promotion}', name: 'app_student_promotion', requirements: ['promotion' => '(\d+)'] )]
    public function getStudentsPerPromotion(Request $request): Response
    {
        $promotion = intval($request->get('promotion'));

        if($promotion < 1 || $promotion > 5){
            return $this->redirectToRoute('app_student');
        }

        $allStudents = $this->studentService->getAllStudentsPerCampus($promotion);

       
        return $this->render('student/filter.html.twig', [
            'promotion' => $promotion,
            'allStudents' => $allStudents
        ]);
    }

    #[Route('/student/details/{id}', name: 'app_student_details', requirements: ['id' => '(\d+)'] )]
    public function getDetailsPerStudent(Request $request): Response
    {
        $idUser = intval($request->get('id'));

        return $this->render('student/details.html.twig', [
        ]);
    }
}

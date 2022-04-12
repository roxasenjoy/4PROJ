<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserExtended;
use App\Form\EditStudentFormType;
use App\Form\RegistrationFormType;
use App\Service\GlobalService;
use App\Service\StudentService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

#[Route('/pedago')]
class StudentController extends AbstractController
{
    public function __construct(StudentService $studentService, EntityManagerInterface $em, GlobalService $globalService){

        $this->em = $em;
        $this->studentService = $studentService;
        $this->globalService = $globalService;

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
        //Récupérer toutes les informations de l'étudiant
        $userId = intval($request->get('id'));

        // Récupérer les éléments via l'id de l'étudiant
        $userDetails = $this->em->getRepository(User::class)->find($userId);
        $compta = $this->globalService->getUserTotalComptability($userId);
        $ects = $this->globalService->getAllEcts($userDetails);

        // Edition de la fiche de l'étudiant
        $user = new User();
        $form = $this->createForm(EditStudentFormType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){



            return $this->redirectToRoute('app_student_promotion');
        }


        return $this->render('student/details.html.twig', [
            'user' => $userDetails,
            'compta' => $compta,
            'ects' => $ects,
            'form' => $form->createView(),
        ]);
    }
}

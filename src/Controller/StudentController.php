<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\Role;
use App\Entity\StudyLevel;
use App\Entity\User;
use App\Entity\UserExtended;
use App\Form\AddStudentFormType;
use App\Form\EditStudentFormType;
use App\Form\RegistrationFormType;
use App\Service\GlobalService;
use App\Service\StudentService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
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
        $userExtendedDetails = $this->em->getRepository(UserExtended::class)->find($userDetails->getUserExtended()->getId());


        $compta = $this->globalService->getUserTotalComptability($userId);
        $ects = $this->globalService->getAllEcts($userDetails);

        // Edition de la fiche de l'étudiant
        $form = $this->createForm(EditStudentFormType::class, $userDetails);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $data = $form->getData();

            // Modification des informations concernant l'utilisateur
            $userDetails
                        ->setFirstName($data->getFirstName())
                        ->setLastName($data->getLastName())
                        ->setCampus($form->get("campus")->getData())
                        ->setEmail($data->getEmail());

            // Modification des éléments concernant les données extended de l'utilisateur
            $userExtendedDetails
                        ->setAddress($form->get("address")->getData())
                        ->setRegion($form->get("region")->getData())
                        ->setActualLevel($form->get("actualLevelName")->getData())
                        ->setHasProContract($form->get("hasProContract")->getData())
                        ->setIsHired($form->get("isHired")->getData());

            $this->em->persist($userDetails);
            $this->em->persist($userExtendedDetails);
            $this->em->flush();

            return $this->redirectToRoute('app_student_details', ['id' => $userId]);
        }


        return $this->render('student/details.html.twig', [
            'user' => $userDetails,
            'compta' => $compta,
            'ects' => $ects,
            'form' => $form->createView(),
        ]);
    }


    #[Route('/student/add', name: 'app_student_add')]
    public function addStudent(Request $request, UserPasswordHasherInterface $userPasswordHasher, TransportInterface $mailer): Response
    {
        $user = new User();
        $userExtended = new UserExtended();
        $isStudent = false;
        $error = "";

        // Edition de la fiche de l'étudiant
        $form = $this->createForm(AddStudentFormType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            switch($form->get('role')->getData()->getId()){
                case 13;
                    $roles = 'ROLE_VISITEUR';
                    break;
                case 14;
                    $roles = 'ROLE_PROFESSEUR';
                    break;
                case 15;
                    $roles = 'ROLE_PEDAGO';
                    break;
                default;
                    $roles = 'ROLE_USER';
                    $isStudent = true;
                    break;
            }


            // On vérifie que l'émail n'est pas déjà utilisé
            if($this->em->getRepository(User::class)->findBy(array('email' => $form->get("email")->getData()))){
                $error = 'L\'adresse email que vous avez utilisé est déjà utilisée pour un autre étudiant. ';
            } else {

                // Génération aléatoire d'un mot de passe
                $password = $this->randomPassword();

                // Modification des informations concernant l'utilisateur
                $user
                    ->setFirstName($form->get("firstName")->getData())
                    ->setLastName($form->get("lastName")->getData())
                    ->setEmail($form->get("email")->getData())
                    ->setPassword($userPasswordHasher->hashPassword($user,$password))
                    ->setRoles(array($roles))
                    ->setCampus($form->get("campus")->getData())
                    ->setRole($form->get('role')->getData())
                ;

                // Modification des éléments concernant les données extended de l'utilisateur
                $userExtended
                    ->setBirthday($form->get('birthday')->getData())
                    ->setAddress($form->get("address")->getData())
                    ->setRegion($form->get("region")->getData())
                    ->setYearEntry($form->get('yearEntry')->getData())
                    ->setYearExit($form->get('yearExit')->getData())
                    ->setActualLevel($form->get("actualLevelName")->getData())
                    ->setNbAbscence(0)
                    ->setIsStudent($isStudent)
                    ->setHasProContract($form->get("hasProContract")->getData())
                    ->setIsHired($form->get("isHired")->getData())
                    ->setActualLevel($form->get('actualLevelName')->getData())
                    ->setPreviousLevel($form->get('lastLevelName')->getData())
                    ->setUser($user)
                ;

                $this->em->persist($user);
                $this->em->persist($userExtended);
                $this->em->flush();

                /* Envoyer un email avec le mot de passe de l'étudiant */

                $email = (new TemplatedEmail())
                    ->from(new Address('madjid@supinfo.com', 'Création de votre compte Madjid Booster'))
                    ->to($user->getEmail())
                    ->subject('Création de votre compte Madjid Booster')
                    ->htmlTemplate('student/emailCreateAccount.html.twig')
                    ->context([
                        'password' => $password,
                    ]);

                $mailer->send($email);


                return $this->redirectToRoute('app_student');
            }
        }

        return $this->render('student/add.html.twig', [
            'form' => $form->createView(),
            'error' => $error,
        ]);
    }

    function randomPassword() {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }
}

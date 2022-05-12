<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\Intervenant;
use App\Entity\Role;
use App\Entity\Subject;
use App\Entity\SubjectDate;
use App\Entity\User;
use App\Entity\UserGrade;
use App\Form\AddCoursForm;
use App\Form\AddHourForm;
use App\Form\EditCoursForm;
use App\Form\FilterCampusForm;
use App\Service\AuthService;
use App\Service\GlobalService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class CoursController extends AbstractController
{

    private AuthService $authService;
    private EntityManagerInterface $em;

    public function __construct(
        EntityManagerInterface $em,
        AuthService $authService,
        GlobalService $globalService,
        TransportInterface $mailer
    )

    {
        $this->em = $em;
        $this->authService = $authService;
        $this->globalService = $globalService;
        $this->mailer = $mailer;
    }

    #[Route('/cours', name: 'app_cours')]
    public function getCours(): Response
    {
        $user = $this->authService->isAuthenticatedUser();

        return $this->render('cours/cours.html.twig', [
            'cours' => $this->globalService->getCours($user),
        ]);
    }

    /* {id} = On passe une variable dynamique, par exemple les id changent en fonction du cours cliqué */
    #[Route('/cours/details/{id}', name: 'app_cours_details')]
    public function getDetails(Request $request): Response
    {

        // Récuperer l'id du cours en question
        $cours = $this->em->getRepository(Subject::class)->find($request->get('id'));

        return $this->render('cours/details.html.twig', [
            'cours' => $cours
        ]);
    }


    /*****************************************************************
     *                      ROLE PEDAGOGIQUE
     *****************************************************************/

    /**
     * Obtenir tous les caurs disponible à SUPINFO
     */
    #[Route('/admin/cours', name: 'app_cours_campus')]
    public function getAllCours(): Response
    {

        $allCours = $this->em->getRepository(Subject::class)->getAllLessons();

        return $this->render('cours/admin/cours.html.twig', [
            'promotion' => 0,
            'allCours' => $allCours
        ]);
    }

    /**
     * Obrenir tous les cours de SUPINFO en fonction de la promotion sélectionnée
     * @param Request $request
     * @return Response
     */
    #[Route('/admin/cours/{promotion}', name: 'app_cours_promotion', requirements: ['promotion' => '(\d+)'] )]
    public function getCoursPerPromotion(Request $request): Response
    {

        $promotion = intval($request->get('promotion'));

        if($promotion < 1 || $promotion > 5){
            return $this->redirectToRoute('app_cours_campus');
        }

        $allCours = $this->em->getRepository(Subject::class)->getAllLessons($promotion);

        return $this->render('cours/admin/cours.html.twig', [
            'promotion' => $promotion,
            'allCours' => $allCours
        ]);
    }

    #[Route('/admin/cours/add', name: 'app_cours_add')]
    public function addCours(Request $request){


        // Ajouter un formulaire qui permet de rajouter les éléments suivants :
        // - Titre du cours
        // - Nom complet du cours
        // - Liste des intervenants en fonction du campus

        $subject = new Subject();
        $error = $errorDiminutif = '';

        $form = $this->createForm(AddCoursForm::class, $subject);
        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid()){

            $diminutif = $form->get('name')->getData();

            $diminutifExist = $this->em->getRepository(Subject::class)->findBy(array('name' => $diminutif));

            if(strlen($diminutif) === 5 && !$diminutifExist) {
                // On récupère toutes les informations des intervenants
                $intervenants = $form->get('intervenants')->getData();

                // On ajoute le cours avant de rajouter les intervenants en question
                $subject->setName($form->get('name')->getData())
                    ->setFullName($form->get('fullName')->getData())
                    ->setPoints($form->get('points')->getData())
                    ->setLevel($form->get('year')->getData());

                foreach ($intervenants as $key => $value) {

                    $intervenant = new Intervenant();
                    $intervenant->setSubject($subject)
                        ->setUser($value['email'])
                        ->setCampus($value['name']);

                    $this->em->persist($subject);
                    $this->em->persist($intervenant);
                    $this->em->flush();
                }

                if(!$intervenants){
                    $this->em->persist($subject);
                    $this->em->flush();
                }

                return $this->redirectToRoute('app_cours_campus');
            } else {

                // Le cours est déjà présent.
                if($diminutifExist){
                    $errorDiminutif = 'Ce cours exite déjà.';
                } else {
                    // Le cours ne possède pas le bon nombre de caractère.
                    $error = 'L\'acronyme n\'est pas valide';
                }
            }
        }

        return $this->render('cours/admin/add.html.twig', [
            'form' => $form->createView(),
            'error' => $error,
            'errorDiminutif' => $errorDiminutif,
        ]);
    }

    /**
     * Afficher le détails du cours (L'intervenant du campus actuel(en fonction de l'utilisateur connecté) + tous les élèves du même campus)
     *
     * Accessible par les rôles suivants :
     *  - Professeur
     *  - L'équipe pédagogique
     *
     * @param Request $request
     * @return Response
     */
    #[Route('/admin/cours/details/{id}', name: 'pedago_cours_details', requirements: ['id' => '(\d+)'] )]
    public function getPedagoCoursDetails(Request $request): Response
    {

        /**
         * Affichage du cours et des éléments correspondants
         */
        $coursId = intval($request->get('id'));
        $error = '';
        $errorNote = '';

        if(!$this->em->getRepository(Subject::class)->find($coursId)){
            return $this->redirectToRoute('app_cours_campus');
        }

        $data = new Campus();
        /* Formulaire pour la modification des notes */
        $form = $this->createForm(FilterCampusForm::class, $data);
        $form->handleRequest($request);
        $filter = $form->get("campus")->getViewData();

        /* Liste des cours et des intervenants */
        $getCours = $this->em->getRepository(Subject::class)->find($coursId);
        $intervenants = $this->em->getRepository(Intervenant::class)->getIntervenantsPerCampus($filter, $coursId);

        /* Liste des étudiants */
        $studentsGrades = $this->em->getRepository(User::class)->getAllStudentPerCours($filter, $coursId);
        $allStudents = $this->em->getRepository(User::class)->getAllStudentsPerLevel($filter, $getCours);

        /* Formulaire pour ajouter une date */

        $errorDate = '';
        $dateCours = new SubjectDate();
        $dateForm = $this->createForm(addHourForm::class, $dateCours);
        $dateForm->handleRequest($request);
        /**
         * Formulaire pour rajouter une heure pour le cours en question
         */
        if($dateForm->isSubmitted() && $dateForm->isValid()){

            $begin = $dateForm->get("dateBegin")->getData();
            $end = $dateForm->get("dateEnd")->getData();


            if($begin > $end){
                $errorDate = "Oups... Le cours se termine avant de commencer";
            } else {

                foreach($allStudents as $studentValue){
                    $dateCours = new SubjectDate();
                    $dateCours  ->setUser($this->em->getRepository(User::class)->find($studentValue['id']))
                                ->setDateBegin($begin)
                                ->setDateEnd($end)
                                ->setSubject($getCours);

                    $this->em->persist($dateCours);
                    $this->em->flush();

                }

                unset($dateForm);
                $dateCours = new SubjectDate();
                $dateForm = $this->createForm(addHourForm::class, $dateCours);

            }

        }

        /**
         * Afficher les étudiants qui ne possèdent pas encore de note
         */
        $combined = array();

        // Afficher les étudiants sans le message d'erreur
        foreach ($allStudents as $student) { // Tous les étudiants

            $comb = array(
                'id' => $student['id'],
                'fullName' => $student['firstName'] . ' ' . $student['lastName'],
                'campusName' => $student['campusName'],
                'grade' => '--',
                'status' => null,
                'errorNote' => '',
            );

            foreach ($studentsGrades as $grade) { // Que les étudiants qui poossèdent une note
                if ($grade['id'] == $student['id']) {
                    $comb['grade'] = $grade['grade'];
                    $comb['status'] = $grade['status'];
                    break;
                }
            }

            $combined[] = $comb;
        }

        /***
         * Édition du cours
         */
        $editForm = $this->createForm(EditCoursForm::class, $getCours);
        $editForm->handleRequest($request);

        // On soumet le formulaire
        if($editForm->isSubmitted()){

            // On récupère les données que l'input nous a donné (Celui qui met les notes)
            foreach($editForm->getExtraData() as $key => $value) {

                $loopStudent = $this->em->getRepository(User::class)->find($key);

                // On détermine si la note est entre 0 et 20
                if(intval($value) >= 0 && intval($value) <= 20) {

                    // Vérifier que l'étudiant possède une note dans cette matière
                    $getStudent = $this->em->getRepository(User::class)->find($key);
                    $idUserGrade = $this->em->getRepository(UserGrade::class)->hasUserGrade($getStudent, $getCours); // Obtenenir l'id du cours

                    // Il possède déjà une note
                    if($value != ""){

                        $value = floatval($value);
                        dump($value);

                        if ($idUserGrade != []) {
                            $hasUserGrade = $this->em->getRepository(UserGrade::class)->find($idUserGrade[0]['id']);

                            if ($hasUserGrade) {

                                $value >= 10 ? $isValid = true : $isValid = false;

                                $hasUserGrade->setStatus($isValid);
                                $hasUserGrade->setGrade($value);

                                $this->em->persist($hasUserGrade);
                                $this->em->flush();

                                if($value <= 9 && $value){
                                    $this->emailFailedCours($getCours, $getStudent, $hasUserGrade->getGrade());
                                }

                            }
                        } else {

                            // Il ne possède pas de note, il faut donc lui en créer une.
                            $newNote = new UserGrade();

                            $newNote->setUser($loopStudent)
                                ->setSubject($getCours)
                                ->setGrade($value)
                                ->setStatus($value >= 10)
                                ->setDate($this->globalService->getTodayDate());

                            $this->em->persist($newNote);
                            $this->em->flush();
                        }
                    }

                } else {
                    $errorNote = 'ERREUR';
                }

                $studentsGrades = $this->em->getRepository(User::class)->getAllStudentPerCours($filter, $coursId);

                /**
                 * Boucle qui permet d'afficher les erreurs en fonction des étudiants
                 */
                $combLoop = array(
                    'id' => $loopStudent->getId(),
                    'fullName' => $loopStudent->getFirstName() . ' ' . $loopStudent->getLastName(),
                    'campusName' => $loopStudent->getCampus()->getName(),
                    'grade' => '--',
                    'status' => null,
                    'errorNote' => intval($value) >= 0 && intval($value) <= 20 ? '' : 'ERREUR'
                );

                foreach ($studentsGrades as $grade) { // Que les étudiants qui possèdent une note
                    if ($grade['id'] == $loopStudent->getId()) {
                        $combLoop['grade'] = $grade['grade'];
                        $combLoop['status'] = $grade['status'];
                        break;
                    }
                }

                $combinedLoop[] = $combLoop;

                $combined = $combinedLoop;

            }

            $error = $this->verificationDiminutif($editForm, $getCours);

        }



        return $this->render('cours/admin/details.html.twig', [
            'form' => $form->createView(),
            'students' => $combined,
            'intervenants' => $intervenants,
            'actualCours' => $getCours,
            'editForm' => $editForm->createView(),
            'errorNote' => $errorNote,
            'error' => $error,
            'coursId' => $coursId,
            'addHour' => $dateForm->createView(),
            'errorDate' => $errorDate
        ]);
    }


    #[Route('/admin/cours/details/{id}/delete', name: 'cours_delete', requirements: ['id' => '(\d+)'] )]
    public function deleteCours(Request $request){

        $coursId = intval($request->get('id'));

        $cours = $this->em->getRepository(Subject::class)->find($coursId);

        $this->em->remove($cours);
        $this->em->flush();

        return $this->redirectToRoute('app_cours_campus');

    }

    public function verificationDiminutif($form, $coursObject){

        $error = '';

        // Si aucun cours, on créer un nouvel objet
        if(!$coursObject){
            $coursObject = new Subject();
        }

        if(strlen($form->getViewData()->getName()) == 5){

            // Update le cours
            $coursObject    ->setName($form->getViewData()->getName())
                            ->setFullName($form->getViewData()->getFullName())
                            ->setLevel($form->get("year")->getData())
                            ->setPoints($form->get("points")->getData());

            $this->em->persist($coursObject);
            $this->em->flush();

        } else {
            $error = 'L\'acronyme n\'est pas valide';
        }

        return $error;
    }


    /**
     * @param $objectCours
     * @param $objectSubject
     * @param $user
     * @return void
     */
    public function emailFailedCours($objectSubject, $user, $note){

        $email = (new TemplatedEmail())
            ->from(new Address('madjid@supinfo.com', 'Reset Email'))
            ->to($this->authService->isAuthenticatedUser()->getEmail())
            ->subject($user->getFirstName() . ' ' . $user->getLastName() . ' n\'a pas validé la matière : ' . $objectSubject->getName())
            ->htmlTemplate('cours/admin/_failed.html.twig')
            ->context([
                'note' => $note,
                'nameStudent' => $user->getFirstName() . ' ' . $user->getLastName(),
                'coursName' => $objectSubject->getName()
            ]);

        $this->mailer->send($email);

    }

}

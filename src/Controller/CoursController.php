<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\Intervenant;
use App\Entity\Notification;
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
use DateTimeZone;
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

        $user = $this->authService->isAuthenticatedUser();

        // Récuperer l'id du cours en question
        $cours = $this->em->getRepository(Subject::class)->find($request->get('id'));
        $intervenant = $this->em->getRepository(Intervenant::class)->getIntervenantBySubject($cours);
        $isValidated = $this->em->getRepository(UserGrade::class)->hasUserGrade($user, $cours);

        $intervenant = $intervenant ? $intervenant[0] : null;
        $isValidated = $isValidated ? $isValidated[0]['grade'] : null;

        return $this->render('cours/details.html.twig', [
            'cours' => $cours,
            'intervenant' => $intervenant,
            'isValidated' => $isValidated
        ]);
    }


    /*****************************************************************
     *                      ROLE PEDAGOGIQUE
     *****************************************************************/

    /**
     *  Obtenir tous les caurs disponible à SUPINFO
     *
        Accessible par les rôles suivants :
           - Professeurs
           - L'équipe pédagogique
     *
     */
    #[Route('/teacher/cours', name: 'app_cours_campus_teacher')]
    #[Route('/admin/cours', name: 'app_cours_campus')]
    public function getAllCours(Request $request): Response
    {
        $user = $this->authService->isAuthenticatedUser();

        $researchBar = $request->query->get('filterValue');

        //Vérification de tous les cours possédés par l'utilisateur connecté
        if($user->getRoles()[0] == 'ROLE_TEACHER'){
            $allCours = $this->em->getRepository(Intervenant::class)->getAllowedSubjectPerIntervenant($user->getId(), $researchBar);
        } else {
            $allCours = $this->em->getRepository(Subject::class)->getAllLessons($researchBar);
        }


        return $this->render('cours/admin/cours.html.twig', [
            'promotion' => 0,
            'allCours' => $this->globalService->generatePagination($allCours, 9, $request)
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

                // Nouvelle notification
                $userConnected  = $this->authService->isAuthenticatedUser();
                $notification   = new Notification();
                $notification   ->setDate($this->globalService->getTodayDate())
                    ->setMessage($subject->getName() . ' vient d\'être créé')
                    ->setCampus($userConnected->getCampus())
                    ->setType('ajoute');

                $this->em->persist($notification);

                foreach ($intervenants as $key => $value) {

                    $intervenant = new Intervenant();
                    $intervenant->setSubject($subject)
                        ->setUser($value['email'])
                        ->setCampus($value['name']);

                    // Nouvelle notification
                    $userConnected  = $this->authService->isAuthenticatedUser();
                    $notificationIntervenant   = new Notification();
                    $notificationIntervenant   ->setDate($this->globalService->getTodayDate())
                        ->setMessage($value['email']->getFirstName() . ' ' . $value['email']->getLastName() . ' vient d\'être assigné à ' . $subject->getName())
                        ->setCampus($userConnected->getCampus())
                        ->setType('ajoute');


                    $this->em->persist($notificationIntervenant);

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
     * Modification du cours + modification des notes
     *
     * @param Request $request
     * @return Response
     */
    #[Route('/teacher/cours/details/{id}/edit', name: 'pedago_cours_details_edit_teacher', requirements: ['id' => '(\d+)'] )]
    #[Route('/admin/cours/details/{id}/edit', name: 'pedago_cours_details_edit', requirements: ['id' => '(\d+)'] )]
    public function editPedagoCours(Request $request): Response
    {

        /**
         * Affichage du cours et des éléments correspondants
         */
        $coursId            = intval($request->get('id'));
        $error              = '';
        $errorNote          = '';
        $user               = $this->authService->isAuthenticatedUser();
        $combined           = array();

        /**
         * Vérification en cas de changement d'url
         */
        // Vérifie sur le prof change d'url
        if($user->getRoles()[0] === "ROLE_TEACHER"){
            if(!$this->em->getRepository(Intervenant::class)->getAllowedSubjectPerIntervenant($user->getId(), null, $coursId)){
                return $this->redirectToRoute('app_cours_campus');
            }
        }

        if(!$this->em->getRepository(Subject::class)->find($coursId)){
            return $this->redirectToRoute('app_cours_campus');
        }

        /* Liste des cours et des intervenants */
        $getCours = $this->em->getRepository(Subject::class)->find($coursId);


        /* Liste des étudiants */
        $studentsGrades = $this->em->getRepository(User::class)->getAllStudentPerCours(null, $coursId);
        $allStudents = $this->em->getRepository(User::class)->getAllStudentsPerLevel(null, $getCours);

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

        $teacherRole = $user->getRoles()[0];

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

                        if ($idUserGrade != []) {
                            $hasUserGrade = $this->em->getRepository(UserGrade::class)->find($idUserGrade[0]['id']);


                            if ($hasUserGrade) {

                                $value >= 10 ? $isValid = true : $isValid = false;

                                // Changement des crédits ECTS de la matière + changement de l'année scolaire
                                if($teacherRole !== 'ROLE_TEACHER') {
                                    $hasUserGrade->setStatus($isValid);
                                    $hasUserGrade->setGrade($value);

                                    $userConnected  = $this->authService->isAuthenticatedUser();
                                    $notification   = new Notification();
                                    if($value){
                                        // Nouvelle notification
                                        $notification   ->setDate($this->globalService->getTodayDate())
                                            ->setMessage('Les notes de ' . $getCours->getName() . ' viennent d\'être modifiées')
                                            ->setCampus($userConnected->getCampus())
                                            ->setType('modifie');

                                    } else {
                                        // Nouvelle notification
                                        $notification   ->setDate($this->globalService->getTodayDate())
                                            ->setMessage($getCours->getName() . ' vient d\'être modifié')
                                            ->setCampus($userConnected->getCampus())
                                            ->setType('modifie');
                                    }

                                    $this->em->persist($notification);
                                    $this->em->persist($hasUserGrade);
                                    $this->em->flush();
                                }

                                // Envoyer des mails à l'équipe pédagogique quand l'utilisateur ne valide pas
                                if($value <= 9 && $value){
                                 //   $this->emailFailedCours($getCours, $getStudent, $hasUserGrade->getGrade());
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

                            $userConnected  = $this->authService->isAuthenticatedUser();
                            $notification   = new Notification();
                            $notification   ->setDate($this->globalService->getTodayDate())
                                ->setMessage('Les notes de ' . $getCours->getName() . ' viennent d\'être modifiées')
                                ->setCampus($userConnected->getCampus())
                                ->setType('modifie');

                            $this->em->persist($newNote);
                            $this->em->flush();
                        }
                    }

                } else {
                    $errorNote = 'ERREUR';
                }


                $studentsGrades = $this->em->getRepository(User::class)->getAllStudentPerCours(null, $coursId);

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

            /*
             * Modification des éléments du cours (Nom, diminutif, notes, années scolaire du cours)
             */
            if($teacherRole !== 'ROLE_TEACHER'){
                $error = $this->verificationDiminutif($editForm, $getCours);
            }
        }

        return $this->render('cours/admin/edit.html.twig', [
            'students'      => $combined,
            'actualCours'   => $getCours,
            'error'         => $error,
            'coursId'       => $coursId,
            'editForm'      => $editForm->createView(),
            'errorNote'     => $errorNote
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
    #[Route('/teacher/cours/details/{id}', name: 'pedago_cours_details_teacher', requirements: ['id' => '(\d+)'] )]
    #[Route('/admin/cours/details/{id}', name: 'pedago_cours_details', requirements: ['id' => '(\d+)'] )]
    public function getPedagoCoursDetails(Request $request): Response
    {


        /**
         * Affichage du cours et des éléments correspondants
         */
        $coursId            = intval($request->get('id'));
        $error              = '';
        $errorNote          = '';
        $user               = $this->authService->isAuthenticatedUser();

        /**
         * Vérification en cas de changement d'url
         */
        // Vérifie sur le prof change d'url
        if($user->getRoles()[0] === "ROLE_TEACHER"){
            if(!$this->em->getRepository(Intervenant::class)->getAllowedSubjectPerIntervenant($user->getId(),null, $coursId)){
                return $this->redirectToRoute('app_cours_campus');
            }
        }

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

                return $this->redirect($request->getUri());
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

        $pagination = $this->globalService->generatePagination($combined, 9, $request);


        return $this->render('cours/admin/details.html.twig', [
            'form' => $form->createView(),
            'students' => $pagination,
            'intervenants' => $intervenants,
            'actualCours' => $getCours,
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

        // Nouvelle notification
        $userConnected  = $this->authService->isAuthenticatedUser();
        $notification   = new Notification();
        $notification   ->setDate($this->globalService->getTodayDate())
            ->setMessage($cours->getName() . " vient d'être supprimé")
            ->setCampus($userConnected->getCampus())
            ->setType('supprime');


        $this->em->persist($notification);
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
            $coursObject    ->setName(strtoupper($form->getViewData()->getName()))
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

    public function emailFailedCours($objectSubject, $user, $note){

        $email = (new TemplatedEmail())
            ->from(new Address('madjid@supinfo.com', 'Évaluation raté'))
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

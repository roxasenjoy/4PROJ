<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\Intervenant;
use App\Entity\Notification;
use App\Entity\Role;
use App\Entity\Subject;
use App\Entity\User;
use App\Entity\UserExtended;
use App\Form\AddIntervenantForm;
use App\Form\AddStudentFormType;
use App\Form\EditIntervenantFormType;
use App\Form\FilterCampusForm;
use App\Service\AuthService;
use App\Service\EmailService;
use App\Service\GlobalService;
use App\Service\RegexService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class IntervenantController extends AbstractController
{

    private AuthService $authService;
    private EntityManagerInterface $em;

    public function __construct(
        EntityManagerInterface $em,
        AuthService $authService,
        EmailService $emailService,
        GlobalService $globalService,
        RegexService $regexService,
    )
    {
        $this->em = $em;
        $this->authService = $authService;
        $this->emailService = $emailService;
        $this->globalService = $globalService;
        $this->regexService = $regexService;
    }

    #[Route('/intervenant', name: 'app_intervenant')]
    public function getIntervenants(): Response
    {
        $user           = $this->authService->isAuthenticatedUser();


        $intervenants   = $this->em->getRepository(Intervenant::class)->getIntervenants($user);



        return $this->render('intervenant/intervenant.html.twig', [
            'intervenants' => $intervenants
        ]);
    }

    /*****************************************************************
     *                      ROLE PEDAGOGIQUE
     *****************************************************************/

    #[Route('/admin/intervenant', name: 'admin_intervenant')]
    public function getAllIntervenantsOnCampus(Request $request){

        $campus = new Campus();
        $form = $this->createForm(FilterCampusForm::class, $campus);
        $form->handleRequest($request);
        $formFilter = $form->get("campus")->getViewData();

        // Récupérer tous les intervenants
        $intervenants = $this->em->getRepository(User::class)->getAllTeacherRoleByCampus($formFilter);

        return $this->render('intervenant/admin/intervenants.html.twig', [
            'intervenants' => $intervenants,
            'form' => $form->createView(),
        ]);

    }

    #[Route('/admin/intervenant/details/{id}', name: 'admin_intervenant_details', requirements: ['id' => '(\d+)'])]
    public function detailsIntervenant(Request $request){

        $idIntervenant              = $request->get('id');
        $user                       = $this->em->getRepository(User::class)->find($idIntervenant);
        $intervenantSubject         = $this->getSubjectByIntervenant($idIntervenant);
        $error                      = '';
        $errorField                 = '';

        $form = $this->createForm(EditIntervenantFormType::class, $user);
        $form->handleRequest($request);

        /**
         * Modification du l'intervenant
         */
        if(!$this->regexService->stringValidation($form->get('firstName')->getData()) ||
            !$this->regexService->stringValidation($form->get('lastName')->getData()))
        {
            $errorField = "ERREUR";
        }

        if($form->isSubmitted() && $form->isValid()){

            /*
             * Ajout des cours en fonction de l'intervenant
             */
            foreach($form->get('subjects') as $subject){

                $campus                     = $subject->get('campus')->getData();
                $subject                    = $subject->get('subject')->getData();
                $isIntervenantAlreadyExist  =  $this->em->getRepository(Intervenant::class)->selectIntervenant(null,$campus->getId(),$subject->getId());

                //On vérifie sur le cours donné n'est pas déjà présent (Même User/Campus/Subject)
                if($isIntervenantAlreadyExist){

                    $fullName   = $isIntervenantAlreadyExist[0]['firstName'] . ' ' . $isIntervenantAlreadyExist[0]['lastName'];
                    $error      = 'Le cours ' . $subject->getName() . ' est déjà donné par : ' . $fullName . ' à ' . $campus->getName();

                } else {
                    $newIntervenant     = new Intervenant();
                    $newIntervenant     ->setCampus($campus)
                                        ->setSubject($subject)
                                        ->setUser($user);

                    // Nouvelle notification
                    $userConnected  = $this->authService->isAuthenticatedUser();
                    $notification   = new Notification();
                    $notification   ->setDate($this->globalService->getTodayDate())
                        ->setMessage($user->getFirstName() . ' ' . $user->getLastName() . ' vient d\'être assigné à ' . $subject->getName())
                        ->setCampus($userConnected->getCampus())
                        ->setType('ajoute');

                    $this->em->persist($notification);
                    $this->em->persist($newIntervenant);
                }
            }

            // Modification des informations de l'intervenant
            $user->getUserExtended()->setRegion($form->get('region')->getData());
            $user->getUserExtended()->setAddress($form->get('address')->getData());

            if(!$error && !$errorField){
                $this->em->flush();
                return $this->redirectToRoute('admin_intervenant_details', array('id' => $idIntervenant));
            }
        }

        return $this->render('intervenant/admin/details.html.twig', [
            'user'                  => $user,
            'intervenantSubject'    => $intervenantSubject,
            'form'                  => $form->createView(),
            'error'                 => $error,
            'errorField'            => $errorField,
            'intervenantId'         => $idIntervenant
        ]);
    }

    #[Route('/admin/intervenants/add', name: 'admin_intervenant_add')]
    public function addIntervenant(Request $request, UserPasswordHasherInterface $userPasswordHasher){

        $user           = new User();
        $userExtended   = new UserExtended();
        $error          = "";

        // Edition de la fiche de l'étudiant
        $form = $this->createForm(AddIntervenantForm::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            // On vérifie que l'émail n'est pas déjà utilisé
            if($this->em->getRepository(User::class)->findBy(array('email' => $form->get("email")->getData()))){
                $error = 'L\'adresse email que vous avez utilisé est déjà utilisée. ';
            } else {

                // Génération aléatoire d'un mot de passe
                $password = $this->globalService->generatePassword();

                // Modification des informations concernant l'utilisateur
                $user
                    ->setFirstName($form->get("firstName")->getData())
                    ->setLastName($form->get("lastName")->getData())
                    ->setEmail($form->get("email")->getData())
                    ->setPassword($userPasswordHasher->hashPassword($user,$password))
                    ->setRoles(array('ROLE_TEACHER'))
                    ->setCampus($form->get("campus")->getData())
                    ->setRole($this->em->getRepository(Role::class)->find(14))
                ;

                // Modification des éléments concernant les données extended de l'utilisateur
                $userExtended
                    ->setBirthday($form->get('birthday')->getData())
                    ->setAddress($form->get("address")->getData())
                    ->setRegion($form->get("region")->getData())
                    ->setYearEntry(0)
                    ->setYearExit(0)
                    ->setActualLevel(null)
                    ->setPreviousLevel(null)
                    ->setNbAbscence(0)
                    ->setIsStudent(false)
                    ->setHasProContract(false)
                    ->setIsHired(false)
                    ->setActualLevel(null)
                    ->setUser($user)
                ;

                // Nouvelle notification
                $userConnected  = $this->authService->isAuthenticatedUser();
                $notification   = new Notification();
                $notification   ->setDate($this->globalService->getTodayDate())
                    ->setMessage("L'intervenant " . $user->getFirstName() . ' ' . $user->getLastName() . " vient d'être ajouté")
                    ->setCampus($userConnected->getCampus())
                    ->setType('ajoute');

                $this->em->persist($notification);

                $this->em->persist($user);
                $this->em->persist($userExtended);
                $this->em->flush();

                /* Envoyer un email avec le mot de passe de l'étudiant */

                $this->emailService->createAccount($user, $password);

                return $this->redirectToRoute('admin_intervenant');
            }
        }

        return $this->render('intervenant/admin/add.html.twig', [
            'form'  => $form->createView(),
            'error' => $error,
        ]);

    }


    #[Route('/admin/intervenants/delete', name: 'admin_intervenant_delete', requirements: ['id' => '(\d+)'])]
    public function deleteSubjectIntervenant(Request $request){

        $idIntervenant      = intval($request->get('intervenantId'));
        $idCampus           = intval($request->get('campusId'));
        $idSubject          = intval($request->get('subjectId'));
        $subject            = $this->em->getRepository(Subject::class)->find($idSubject);
        $deleteId           = $this->em->getRepository(Intervenant::class)->selectIntervenant($idIntervenant, $idCampus, $idSubject);
        $objectIntervenant  = $this->em->getRepository(Intervenant::class)->find($deleteId[0]['id']);

        // Nouvelle notification
        $userConnected  = $this->authService->isAuthenticatedUser();
        $notification   = new Notification();
        $notification   ->setDate($this->globalService->getTodayDate())
            ->setMessage("Le cours " . $subject->getName() . " n'est plus attribué à " . $objectIntervenant->getUser()->getFirstName() . ' ' . $objectIntervenant->getUser()->getLastName())
            ->setCampus($userConnected->getCampus())
            ->setType('supprime');

        $this->em->persist($notification);
        $this->em->remove($objectIntervenant);
        $this->em->flush();

        return $this->redirectToRoute('admin_intervenant_details', array('id' => $idIntervenant));

    }

    #[Route('/admin/intervenants/delete/{id}', name: 'intervenant_delete', requirements: ['id' => '(\d+)'])]
    public function deleteIntervenant(Request $request){

        $idIntervenant      = intval($request->get('id'));

        $intervenant        = $this->em->getRepository(User::class)->find($idIntervenant);

        // Nouvelle notification
        $userConnected  = $this->authService->isAuthenticatedUser();
        $notification   = new Notification();
        $notification   ->setDate($this->globalService->getTodayDate())
            ->setMessage("L'intervenant " . $intervenant->getFirstName() . ' ' . $intervenant->getLastName() . " vient d'être supprimé")
            ->setCampus($userConnected->getCampus())
            ->setType('supprime');

        $this->em->persist($notification);
        $this->em->remove($intervenant);
        $this->em->flush();

        return $this->redirectToRoute('admin_intervenant');

    }


    public function getSubjectByIntervenant($intervenantId){

        $intervenants       = $this->em->getRepository(Intervenant::class)->getSubjectByIntervenant($intervenantId);
        $subjects           = $this->em->getRepository(Subject::class)->getAllLessons();
        $combined           = [];

        // Lier les intervenants au cours associé
        foreach ($subjects as $subject) { // Tous les étudiants

            $comb = array(
                'id'            => $subject['id'],
                'name'          => $subject['name'],
                'fullName'      => $subject['fullName'],
                'levelName'     => $subject['levelName'],
                'levelYear'     => $subject['levelYear'],
                'intervenantId' => null,
                'firstName'     => '--',
                'lastName'      => '--',
                'campusId'      => null,
                'campusName'    => '--',
            );

            foreach ($intervenants as $intervenant) { // Que les étudiants qui poossèdent une note
                if ($subject['id'] == $intervenant['idSubject']  ) {
                    $comb['intervenantId']  = $intervenant['id'];
                    $comb['firstName']      = $intervenant['firstName'];
                    $comb['lastName']       = $intervenant['lastName'];
                    $comb['campusId']       = $intervenant['idCampus'];
                    $comb['campusName']     = $intervenant['campusName'];

                    $combined[] = $comb;
                }
            }
        }

       // Enleve les array en double.
        $combined       = array_map("unserialize", array_unique(array_map("serialize", $combined)));
        // On tri les données pour qu'elles se retrouvent dans l'ordre
        $sortCombined   = array();
        foreach ($combined as $key => $row)
        {
            $sortCombined[$key] = $row['name'];
        }

        array_multisort($sortCombined, SORT_ASC, $combined);

        return $combined;


    }

}

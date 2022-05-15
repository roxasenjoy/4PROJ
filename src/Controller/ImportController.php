<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\Import;
use App\Entity\Role;
use App\Entity\StudyLevel;
use App\Entity\User;
use App\Entity\UserExtended;
use App\Form\ImportType;
use App\Service\AuthService;
use App\Service\EmailService;
use App\Service\FileUploader;
use App\Service\GlobalService;
use App\Service\ImportService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;

class ImportController extends AbstractController
{

    CONST alternance = "first_name;last_name;date_of_birth;year_of_birth;street_address;email;gender;region;campus;level;contratPro;is_hired;lenght_month_hired;speciality;company_hired;entreprise_alternance;entreprise_alternance_address;poste_occupe;secteur_activite_entreprise_alternance;date_debut_alternance";
    CONST campusStaff = "id;first_name;last_name;email;Campus;Roles";
    CONST administratif = 29;
    CONST comptability = "first_name;last_name;date_of_birth;year_of_birth;street_address;email;gender;region;campus;entry_level;year_of_entry;year_of_exit;study_lenght;level_of_exit;still_student;level;contratPro;speciality;compta_paymentType;compta_paid;compta_paymentDue;compta_relance";
    CONST notes = "id;first_name;last_name;email;campus;level;speciality;1WORK;1WDEV;1ITWO;1TEAM;1PYTH;1O365;2JAVA;2PHPD;2GRAP;2DTTL;2DVST;2AWSP;3ANDM;3CCNA;3ASPC;3LPIC;3AGIL;4AZUR;4BOSS;4GDPR;4DOCKR;4CHGM;4BINT;4SECU;5CCNA;5DATA;5DOOP;5ITIL;5RBIG;5BLOC;5MDD";
    CONST intervenants = "";

    public function __construct(
        EntityManagerInterface $em,
        ImportService $importService,
        GlobalService $globalService,
        EmailService $emailService,
    )

    {
        $this->em = $em;
        $this->importService = $importService;
        $this->globalService = $globalService;
        $this->emailService = $emailService;
    }

    #[Route('/import', name: 'import')]
    public function index(Request $request, FileUploader $fileUploader, UserPasswordHasherInterface $userPasswordHasher): Response
    {

        $import = new Import();
        $form = $this->createForm(ImportType::class, $import);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $importFile = $form->get('import')->getData();

            if ($importFile) {
                $importFileName = $fileUploader->upload($importFile);

                // On ouvre le fichier qu'on vient de récupérer
                if (($handle = fopen('uploads/import/' .$importFileName, "r")) !== false) {

                    $headerArray = fgetcsv($handle, 2000, ';');

                    // On boucle sur tous les éléments de l'excel afin d'obtenir les informations qui s'y trouvent
                    while (($data = fgetcsv($handle)) !== false) {

                        // Modifie les lignes imbuvables par un array lisible
                        $detailsData = explode(';', $data[0]);
                        $keyWithData = array_combine($headerArray, $detailsData);

                        $this->setDataInDatabase(count($headerArray), $keyWithData, $userPasswordHasher);
                    }
                    fclose($handle);
                }


                $import->setFileName($importFileName);
                $this->em->persist($import);
                $this->em->flush();

            }

            return $this->redirectToRoute('import');
        }

        return $this->renderForm('import/show.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * Insérer les informations en fonction de l'excel que nous allons envoyer (En partant du principe qu'ils ont toujours la même tête)
     * @param $nbElementInHeader
     * @param $data
     * @param $userPasswordHasher
     * @return void
     */
    public function setDataInDatabase($nbElementInHeader, $data, $userPasswordHasher){

        /*
         * Gestion des différents excels présent dans la base de données
         */
        ini_set('max_execution_time', 500);

        switch($nbElementInHeader){
            case self::alternance:

                break;

            case self::comptability:
                break;


            /**
             * Gestion des nouveaux utilisateurs
             *  - Si l'utilisateur n'est pas présent, on l'ajoute dans la base de données avec un nouvel user_extended
             *  - L'utilisateur est déjà présent ? On update les informations
             */
            case self::administratif:

                // On regarde si l'utilisateur est déjà présent
                $isUserExist = $this->em->getRepository(User::class)->findBy(array('email'  => $data['email']));

                // L'utilisateur n'est pas présent dans la base de données
                if(!$isUserExist && $data['first_name'] && $data['email'] && $data['date_of_birth']){
                    /**
                     * L'utilisateur n'est pas présent dans la base de données, il faut le rajouter
                     **/

                    $campusId           = $this->importService->getCampusEntity($data['campus']);
                    $birthday           = $this->importService->setDateIntoDatetime($data['date_of_birth']);
                    $isStudent          = filter_var($data['still_student'], FILTER_VALIDATE_BOOLEAN);
                    $nbMissing          = intval($data['nbre_absence']);
                    $hasProContract     = filter_var($data['contratPro'], FILTER_VALIDATE_BOOLEAN);
                    $isHired            = filter_var($data['is_hired'], FILTER_VALIDATE_BOOLEAN);
                    $getExitLevel       = $this->importService->getExitLevel($data);
                    $getActualLevel     = $this->importService->getLevelId($data); // A REVOIR
                    $getPreviousLevel   = $this->importService->getPreviousLevel($data['previous_level']);

                    $newUser            = new User();
                    $newUserExtended    = new UserExtended();

                    //Génération du password qui sera envoyer par email
                    // Génération aléatoire d'un mot de passe
                    $password = $this->globalService->generatePassword();

                    //On créer le nouvel utilisateur (User)
                    // Création d'un mot de passe poubelle qu'on va envoyer par mail
                    $newUser->setFirstName($data['first_name'])
                        ->setLastName($data['last_name'])
                        ->setEmail($data['email'])
                        ->setCampus($this->em->getRepository(Campus::class)->find($campusId))
                        ->setRole($this->em->getRepository(Role::class)->find(12))
                        ->setRoles(array('ROLE_USER'))
                        ->setPassword($userPasswordHasher->hashPassword($newUser,$password))
                    ;


                    $newUserExtended
                        ->setBirthday($birthday)
                        ->setAddress($data['street_address'])
                        ->setRegion($data['region'])
                        ->setYearEntry($data['year_of_entry'])
                        ->setYearExit($getExitLevel)
                        ->setActualLevel($getActualLevel) // Niveau actuel
                        ->setPreviousLevel($this->em->getRepository(StudyLevel::class)->find($getPreviousLevel)) // Niveau de l'année précédent son année actuelle
                        ->setNbAbscence($nbMissing)
                        ->setIsStudent($isStudent)
                        ->setHasProContract($hasProContract)
                        ->setIsHired($isHired)
                        ->setUser($newUser)
                    ;


                    $this->em->persist($newUser);
                    $this->em->persist($newUserExtended);
                    $this->em->flush();


                    // On rajoute les données supplémentaires à l'utilisateur (UserExtended)
                    // Envoyer un email pour que l'utilisateur puisse effectuer sa première connexion
                    // $this->emailService->createAccount($newUser, $password);
                }




                break;

            case self::campusStaff:
                break;

            case self::notes:
                break;

            default:
                break;

        }

    }
}

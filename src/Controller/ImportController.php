<?php

namespace App\Controller;


use App\Entity\Import;
use App\Entity\Notification;
use App\Entity\Subject;
use App\Entity\User;
use App\Entity\UserExtended;
use App\Form\ImportType;
use App\Message\ImportData;
use App\MessageHandler\ImportDataHandler;
use App\Service\AuthService;
use App\Service\EmailService;
use App\Service\FileUploader;
use App\Service\GlobalService;
use App\Service\ImportService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\ImmutableEventDispatcher;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class ImportController extends AbstractController
{

    CONST alternance = "id,student,contrat,companyName,topay_student,topay_company,hire_date"; // Terminé
    CONST administratif = "id,first_name,last_name,campus,cursus"; // Terminé
    CONST comptability = "id,student_id,amount_due,percent_paid,amount_paid"; // Terminé
    CONST notes = "id,cursus,module,student,grade";
    CONST modules = "id,moduleId,moduleName,moduleDescription,credits,cursus";

    public function __construct(
        EntityManagerInterface $em,
        ImportService $importService,
        GlobalService $globalService,
        EmailService $emailService,
        AuthService $authService,
        UserPasswordHasherInterface $userPasswordHasher,
        FileUploader $fileUploader,
    )

    {
        $this->em = $em;
        $this->importService = $importService;
        $this->userPasswordHasher = $userPasswordHasher;
        $this->globalService = $globalService;
        $this->fileUploader = $fileUploader;
        $this->emailService = $emailService;
        $this->authService = $authService;
    }

    #[Route('/import', name: 'import')]
    public function index(Request $request, FileUploader $fileUploader, UserPasswordHasherInterface $userPasswordHasher,MessageBusInterface $bus): Response
    {

        // Set password
        $newUser            = new User();
        $password           = $this->userPasswordHasher->hashPassword($newUser,'generalPassword');

        $import = new Import();
        $form = $this->createForm(ImportType::class, $import);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $importFile = $form->get('import')->getData();

            if ($importFile) {
                $importFileName = $fileUploader->upload($importFile);


                $import = new ImportData($importFileName);
                // $bus->dispatch($import); // Méthode asynchrone ( INUTILE VU QUE LES DUMP RALENTISSENT LE CODE)

                $baseUrl = getcwd();

                $path = $baseUrl . "\uploads\import\\" . $import;

                $i = 0;

                // On ouvre le fichier qu'on vient de récupérer
                if (($handle = fopen($path, "r")) !== false) {

                    $headerArray = fgetcsv($handle, 2000, ',');
                    $stringHeaderArray = '';

                    // On ajoute tous les éléments pour créer une string
                    foreach ($headerArray as $key => $value) {
                        $stringHeaderArray .= $value . ',';
                    }
                    //On enleve le dernier caractère qui n'a rien à faire là
                    $stringHeaderArray = rtrim($stringHeaderArray, ", ");
                    set_time_limit(300);

                    // On boucle sur tous les éléments de l'excel afin d'obtenir les informations qui s'y trouvent
                    while (($data = fgetcsv($handle)) !== false) {

                        $keyWithData = array_combine($headerArray, $data);

                        $i++;

                        // Ici on import toutes les données - Le mot de passe est prédéfini car il est trop long à compiler et l'async ne peut pas fonctionner
                        $this->setDataInDatabase($stringHeaderArray,$keyWithData, $i, $password);

                    }
                    fclose($handle);
                }

                // Nouvelle notification
                $userConnected  = $this->authService->isAuthenticatedUser();
                $notification   = new Notification();
                $notification   ->setDate($this->globalService->getTodayDate())
                    ->setMessage($userConnected->getFirstName() . ' ' . $userConnected->getLastName() . " vient d'importer un fichier")
                    ->setCampus($userConnected->getCampus())
                    ->setType('ajoute');

                $this->em->persist($notification);
                $this->em->flush();
//                $import->setFileName($importFileName);
//                $this->em->persist($import);
//                $this->em->flush();

            }

            return $this->redirectToRoute('import');
        }

        return $this->renderForm('import/show.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * @param $elementsInHeader
     * @param $data
     * @return void
     */
    public function setDataInDatabase($elementsInHeader, $data, $i, $password){

        $birthday = $today  = date("Y-m-d H:i:s"); // Validé

        switch($elementsInHeader){
            case self::alternance:

                $studentId          = $data['student'];

                /**
                 * Est ce que les étudiants possèdent une alternance ? Si oui on modifie la valeur dans notre base de données
                 */
                $sqlHasAlternance = "    UPDATE user_extended
                                SET has_pro_contract = 1
                                where
                                    user_id = (SELECT id FROM user WHERE id_extended='$studentId')";

                //Lancement de la requête
                $hasAlternanceStatement = $this->em->getConnection()->prepare($sqlHasAlternance);
                $hasAlternanceStatement->execute();
                break;

            case self::comptability:

                $credit             = intval($data['amount_due']);
                $debit              = intval($data['amount_paid']);
                $studentId          = $data['student_id'];

                /**
                 * La comptabilié est déjà présente, on ne le rajoute pas
                 **/
                $sqlCompta = " INSERT IGNORE INTO user_comptability
                                    (user_id, date, description, debit, credit)
                              VALUES
                                    ((SELECT id FROM user WHERE id_extended='$studentId'), '$today', 'Aucune description', '$credit', '$debit')";

                //Lancement de la requête
                $comptaStatement = $this->em->getConnection()->prepare($sqlCompta);
                $comptaStatement->execute();
                break;


            /**
             * Gestion des nouveaux utilisateurs
             *  - Si l'utilisateur n'est pas présent, on l'ajoute dans la base de données avec un nouvel user_extended
             *  - L'utilisateur est déjà présent ? On update les informations
             */
            case self::administratif:

                $data = $this->cleanString($data);

                $first_name         = strtolower($data['first_name']);
                $last_name          = strtolower($data['last_name']);
                $email              = $first_name . $last_name . '@supinfo.com';
                $idExtended         = $data['id'];

                $region             = 'À définir';

                // Traitement du fichier ACCOUNTING


                // Traitement du fichier : STUDENT
                $yearEntry          = $this->importService->getYearEntry($data);
                $campusId           = $this->importService->getCampusEntity($data['campus']); // Validé

                $yearExit           = $this->importService->getYearExit($data);
                $getActualLevel     = $this->importService->getActualLevel($data);
                $getPreviousLevel   = 6; //BAC

                /**
                 * L'utilisateur n'est pas présent dans la base de données, il faut le rajouter
                 **/
                $sqlUser = " INSERT IGNORE INTO user
                            (email, password, roles, role_id, campus_id, `first_name`, `last_name`, id_extended)
                      VALUES
                            ('$email', '$password', '" . '["ROLE_USER"]' ."', 12, '$campusId', '$first_name', '$last_name', '$idExtended')";


                //Lancement de la requête
                $userStatement = $this->em->getConnection()->prepare($sqlUser);
                $userStatement->execute();


                /**
                 * On ajoute à l'utilisateur qui vient d'être créé, son user_extended vu que c'est des étudiants
                 */
                $sqlUserExtended = " INSERT IGNORE INTO user_extended
                            (user_id, actual_level_id, previous_level_id, birthday, address, region, year_entry, year_exit, nb_abscence, is_student, has_pro_contract, is_hired)
                      VALUES
                            ((SELECT id FROM user WHERE email='$email'),'$getActualLevel', '$getPreviousLevel', '$birthday', 'Adresse à définir', '$region', '$yearEntry', '$yearExit', 0, 1, 0, 0)";

                $userExtendedStatement = $this->em->getConnection()->prepare($sqlUserExtended);
                $userExtendedStatement->execute();


                // On rajoute les données supplémentaires à l'utilisateur (UserExtended)
                // Envoyer un email pour que l'utilisateur puisse effectuer sa première connexion
                // $this->emailService->createAccount($newUser, $password);
                break;

            case self::modules:
                $points                 = $data['credits'];
                $subjectName            = $data['moduleId'];
                $subjectFullName        = $data['moduleName'];
                $subjectDescription     = $data['moduleDescription'];

                /**
                 * La comptabilié est déjà présente, on ne le rajoute pas
                 **/

                if($this->em->getRepository(Subject::class)->findBy(array('name' => $subjectName))){
                    break;
                }

                $sqlModules = "INSERT IGNORE INTO subject
                                    (level_id, name, full_name, points, description)
                              VALUES
                                    ('$subjectName[0]', '$subjectName', '$subjectFullName', '$points', '$subjectDescription')";

                //Lancement de la requête
                $modulesStatement = $this->em->getConnection()->prepare($sqlModules);
                $modulesStatement->execute();
                break;

            case self::notes:
                $studentId          = $data['student'];
                $subjectName        = $data['module'];
                $note               = $data['grade'];
                $statut             = 0;

                if($note >= 10){
                    $statut = 1;
                }


                /**
                 * La comptabilié est déjà présente, on ne le rajoute pas
                 **/
                $sqlNotes = "INSERT IGNORE INTO user_grade
                                    (user_id, subject_id, grade, status, date)
                              VALUES
                                    (
                                    (SELECT id FROM user WHERE id_extended='$studentId'), 
                                    (SELECT id FROM subject WHERE name ='$subjectName'),
                                    $note,
                                    $statut,
                                    '$today'
                                    )"
                                ;

                //Lancement de la requête
                $notesStatement = $this->em->getConnection()->prepare($sqlNotes);
                $notesStatement->execute();
                break;

            default:
                break;

        }

    }

    /**
     * Permet d'enlever les caractères spéciaux des strings
     * @param $data
     * @return array|string|string[]|null
     */
    public function cleanData($data){
        return preg_replace('/[^A-Za-z]/', '', $data);
    }

    /**
     * Retourne les valeurs nettoyées
     * @param $data
     * @return mixed
     */
    public function cleanString($data){

        $data['first_name']         = $this->cleanData($data['first_name']);
        $data['last_name']          = $this->cleanData($data['last_name']);

        return $data;
    }

}

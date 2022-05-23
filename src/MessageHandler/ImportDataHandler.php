<?php

namespace App\MessageHandler;

use App\Entity\User;
use App\Entity\UserExtended;
use App\Message\ImportData;
use App\Service\FileUploader;
use App\Service\GlobalService;
use App\Service\ImportService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\This;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ImportDataHandler implements MessageHandlerInterface
{

    CONST alternance = "first_name;last_name;date_of_birth;year_of_birth;street_address;email;gender;region;campus;level;contratPro;is_hired;lenght_month_hired;speciality;company_hired;entreprise_alternance;entreprise_alternance_address;poste_occupe;secteur_activite_entreprise_alternance;date_debut_alternance";
    CONST campusStaff = "id;first_name;last_name;email;Campus;Roles";
    CONST administratif = 29;
    CONST comptability = "first_name;last_name;date_of_birth;year_of_birth;street_address;email;gender;region;campus;entry_level;year_of_entry;year_of_exit;study_lenght;level_of_exit;still_student;level;contratPro;speciality;compta_paymentType;compta_paid;compta_paymentDue;compta_relance";
    CONST notes = "id;first_name;last_name;email;campus;level;speciality;1WORK;1WDEV;1ITWO;1TEAM;1PYTH;1O365;2JAVA;2PHPD;2GRAP;2DTTL;2DVST;2AWSP;3ANDM;3CCNA;3ASPC;3LPIC;3AGIL;4AZUR;4BOSS;4GDPR;4DOCKR;4CHGM;4BINT;4SECU;5CCNA;5DATA;5DOOP;5ITIL;5RBIG;5BLOC;5MDD";
    CONST intervenants = "";

    /**
     * @param GlobalService $globalService
     * @param UserPasswordHasherInterface $userPasswordHasher
     * @param EntityManagerInterface $em
     */
    public function __construct(
        GlobalService $globalService,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $em,
        FileUploader $fileUploader,
        ImportService $importService)
    {
        $this->globalService = $globalService;
        $this->userPasswordHasher = $userPasswordHasher;
        $this->em = $em;
        $this->fileUploader = $fileUploader;
        $this->importService = $importService;

    }

    /**
     * @param ImportData $import
     * @return void
     */
    public function __invoke(ImportData $import)
    {

        $baseUrl = getcwd();
        $path = $baseUrl . "\public\uploads\import\\" . $import;
        $i = 0;

        // On ouvre le fichier qu'on vient de récupérer
        if (($handle = fopen($path, "r")) !== false) {

            $headerArray = fgetcsv($handle, 2000, ';');

            // On boucle sur tous les éléments de l'excel afin d'obtenir les informations qui s'y trouvent
            while (($data = fgetcsv($handle)) !== false) {

                // Modifie les lignes imbuvables par un array lisible
                $detailsData = explode(';', $data[0]);
                $keyWithData = array_combine($headerArray, $detailsData);

                $i++;

                $keyWithData = $this->setCleanData($keyWithData);
                if($keyWithData['first_name'] && $keyWithData['email']){
                    $this->setDataInDatabase(count($headerArray), $keyWithData, $i);
                }
            }
            fclose($handle);
        }


    }

    /**
     * Retourne les valeurs nettoyées
     * @param $data
     * @return mixed
     */
    public function setCleanData($data){

        $data['first_name']         = $this->cleanData($data['first_name']);
        $data['last_name']          = $this->cleanData($data['last_name']);
        $data['email']              = $this->cleanData($data['email']);

        return $data;
    }


    /**
     * @param $nbElementInHeader
     * @param $data
     * @return void
     */
    public function setDataInDatabase($nbElementInHeader, $data, $i){

        $newUser            = new User();
        $newUserExtended    = new UserExtended();

        $email              = $data['email'];
        $first_name         = $data['first_name'];
        $last_name          = $data['last_name'];
        $address            = $data['street_address'];
        $region             = $data['region'];
        $yearEntry          = $data['year_of_entry'];

        // Traitement des données pour les ajouter à ma DB
        $campusId           = $this->importService->getCampusEntity($data['campus']);
        $birthday           = $this->importService->setDateIntoDatetime($data['date_of_birth'])->format('Y-m-d H:i:s');
        $isStudent          = filter_var($data['still_student'], FILTER_VALIDATE_BOOLEAN);
        $nbMissing          = intval($data['nbre_absence']);
        $hasProContract     = filter_var($data['contratPro'], FILTER_VALIDATE_BOOLEAN);
        $isHired            = filter_var($data['is_hired'], FILTER_VALIDATE_BOOLEAN);
        $getExitLevel       = $this->importService->getExitLevel($data);
        $getActualLevel     = $this->importService->getLevelId($data); // A REVOIR
        $getPreviousLevel   = $this->importService->getPreviousLevel($data['previous_level']);

        // Set password
        $password           = $this->globalService->generatePassword();
        $password           = $this->userPasswordHasher->hashPassword($newUser,$password);



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

                /**
                 * L'utilisateur n'est pas présent dans la base de données, il faut le rajouter
                 **/
                $sqlUser = " INSERT IGNORE INTO user
                            (email, password, roles, role_id, campus_id, `first_name`, `last_name`)
                      VALUES
                            ('$email', '$password', '" . '["ROLE_USER"]' ."', 12, '$campusId', '$first_name', '$last_name')";

                $userStatement = $this->em->getConnection()->prepare($sqlUser);
                $userStatement->execute();

                dump("Utilisateur n°" . $i . " ajouté : " . $email);


                $sqlUserExtended = " INSERT IGNORE INTO user_extended
                            (user_id, actual_level_id, previous_level_id, birthday, address, region, year_entry, year_exit, nb_abscence, is_student, has_pro_contract, is_hired)
                      VALUES
                            ((SELECT id FROM user WHERE email='$email'),'$getActualLevel', '$getPreviousLevel', '$birthday', '$address', '$region', '$yearEntry', '$getExitLevel', '$nbMissing', '$isStudent', '$hasProContract', '$isHired')";

                $userExtendedStatement = $this->em->getConnection()->prepare($sqlUserExtended);
                $userExtendedStatement->execute();

                dump("Données supplémentaires ajoutés  : " . $i);


                // On rajoute les données supplémentaires à l'utilisateur (UserExtended)
                // Envoyer un email pour que l'utilisateur puisse effectuer sa première connexion
                // $this->emailService->createAccount($newUser, $password);


                break;

            case self::campusStaff:
                break;

            case self::notes:
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


}
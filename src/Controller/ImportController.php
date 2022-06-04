<?php

namespace App\Controller;


use App\Entity\Import;
use App\Entity\Notification;
use App\Entity\User;
use App\Entity\UserExtended;
use App\Form\ImportType;
use App\Message\ImportData;
use App\Message\ImportNotification;
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



    public function __construct(
        EntityManagerInterface $em,
        ImportService $importService,
        GlobalService $globalService,
        EmailService $emailService,
        AuthService $authService,
    )

    {
        $this->em = $em;
        $this->importService = $importService;
        $this->globalService = $globalService;
        $this->emailService = $emailService;
        $this->authService = $authService;
    }

    #[Route('/import', name: 'import')]
    public function index(Request $request, FileUploader $fileUploader, UserPasswordHasherInterface $userPasswordHasher,MessageBusInterface $bus): Response
    {

        $import = new Import();
        $form = $this->createForm(ImportType::class, $import);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $importFile = $form->get('import')->getData();

            if ($importFile) {
                $importFileName = $fileUploader->upload($importFile);

                // Méthode asynchrone
                $import = new ImportData($importFileName);
                $bus->dispatch($import);

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

}

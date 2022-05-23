<?php

namespace App\Controller;


use App\Entity\Import;
use App\Entity\User;
use App\Entity\UserExtended;
use App\Form\ImportType;
use App\Message\ImportData;
use App\Message\ImportNotification;
use App\MessageHandler\ImportDataHandler;
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
    )

    {
        $this->em = $em;
        $this->importService = $importService;
        $this->globalService = $globalService;
        $this->emailService = $emailService;
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

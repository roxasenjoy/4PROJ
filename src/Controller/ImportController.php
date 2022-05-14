<?php

namespace App\Controller;

use App\Entity\Import;
use App\Form\ImportType;
use App\Service\AuthService;
use App\Service\FileUploader;
use App\Service\GlobalService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;

class ImportController extends AbstractController
{

    public function __construct(
        EntityManagerInterface $em
    )

    {
        $this->em = $em;
    }

    #[Route('/import', name: 'import')]
    public function index(Request $request, FileUploader $fileUploader): Response
    {

        $import = new Import();
        $form = $this->createForm(ImportType::class, $import);
        $form->handleRequest($request);

        $i = 0;

        if ($form->isSubmitted() && $form->isValid()) {

            $brochureFile = $form->get('import')->getData();

            if ($brochureFile) {
                $brochureFileName = $fileUploader->upload($brochureFile);

                // On ouvre le fichier qu'on vient de récupérer
                if (($handle = fopen('uploads/import/' .$brochureFileName, "r")) !== false) {

                    // On boucle sur tous les éléments de l'excel afin d'obtenir les informations qui s'y trouvent
                    while (($data = fgetcsv($handle)) !== false && $i < 10) {

                        // Modifie les lignes imbuvables par un array lisible
                        dump(explode(';', $data[0]));


                        $i++;

                    }


                    dd('test');
                    fclose($handle);
                }


//                $import->setFileName($brochureFileName);
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

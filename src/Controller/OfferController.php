<?php

namespace App\Controller;

use App\Entity\Offer;
use App\Form\AddOfferForm;
use App\Form\EditOfferForm;
use App\Service\GlobalService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OfferController extends AbstractController
{

    public function __construct(GlobalService $globalService, EntityManagerInterface $em){
        $this->globalService = $globalService;
        $this->em = $em;
    }

    #[Route('/offres/add', name: 'add_offer')]
    public function addOffer(Request $request){

        $errorMessage = "";

        // Création du formulaire
        $offer = new Offer();
        $form = $this->createForm(AddOfferForm::class, $offer);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $title          = $form->get('title')->getData();
            $company        = $form->get('company')->getData();
            $typeContract   = $form->get('type_contract')->getData();
            $location       = $form->get('location')->getData();
            $description    = $form->get('description')->getData();
            $profil         = $form->get('profil')->getData();
            $salaire        = $form->get('salaire')->getData();
            $experience     = $form->get('experience')->getData();
            $website        = $form->get('website')->getData();

            if( $title  === null ||
                $company  === null ||
                $typeContract  === null ||
                $location  === null ||
                $description  === null ||
                $profil  === null ||
                $salaire  === null)
            {

                // Le formulaire n'est pas valide
                $errorMessage = 'Les informations saisies ne sont pas valides.';
            } else {
                // Le formulaire est valide, on peut le rajouter dans notre base de données

                $offer->setTitle($title)
                        ->setCompany($company)
                        ->setTypeContract($typeContract)
                        ->setLocation($location)
                        ->setDatePublication($this->globalService->getTodayDate())
                        ->setDescription($description)
                        ->setProfil($profil)
                        ->setSalaire($salaire)
                        ->setExperience($experience);

                if($website){
                    $offer->setWebsite($website);
                }

                $this->em->persist($offer);
                $this->em->flush();

                return $this->redirectToRoute('show_offer');
            }
        }


        return $this->render('offer/add.html.twig', [
            'form' => $form->createView(),
            'errorMessage' => $errorMessage
        ]);
    }

    #[Route('/offres/remove/{id}', name: 'remove_offer')]
    public function removeOffer(Request $request){

        $id = intval($request->get('id'));
        $offer = $this->em->getRepository(Offer::class)->find($id);

        if($offer){
            $this->em->remove($offer);
            $this->em->flush();
        }



        return $this->redirectToRoute('show_offer');

    }

    #[Route('/offres/edit/{id}', name: 'edit_offer')]
    public function editOffer(Request $request){

        $id = intval($request->get('id'));
        $offer = $this->em->getRepository(Offer::class)->find($id);

        // On récupère l'offre déjà présente afin d'utiliser les informations dans la DB
        // On pré-remplira les éléments
        $form = $this->createForm(EditOfferForm::class, $offer);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $title = $form->get('title')->getData();
            $company = $form->get('company')->getData();
            $typeContract = $form->get('type_contract')->getData();
            $location = $form->get('location')->getData();
            $description = $form->get('description')->getData();
            $profil = $form->get('profil')->getData();
            $salaire = $form->get('salaire')->getData();
            $experience = $form->get('experience')->getData();
            $website = $form->get('website')->getData();

            if ($title === null ||
                $company === null ||
                $typeContract === null ||
                $location === null ||
                $description === null ||
                $profil === null ||
                $salaire === null) {

                // Le formulaire n'est pas valide
                $errorMessage = 'Les informations saisies ne sont pas valides.';
            } else {
                // Le formulaire est valide, on peut le rajouter dans notre base de données

                $offer->setTitle($title)
                    ->setCompany($company)
                    ->setTypeContract($typeContract)
                    ->setLocation($location)
                    ->setDatePublication($this->globalService->getTodayDate())
                    ->setDescription($description)
                    ->setProfil($profil)
                    ->setSalaire($salaire)
                    ->setExperience($experience);

                if ($website) {
                    $offer->setWebsite($website);
                }

                $this->em->persist($offer);
                $this->em->flush();

                return $this->redirectToRoute('show_offer');
            }
        }

        return $this->render('offer/edit.html.twig', [
            'errorMessage' => '',
            'form' => $form->createView()
        ]);

    }

    #[Route('/offres/details/{id}', name: 'details_offer')]
    public function detailsOffer(Request $request){

        $id = intval($request->get('id'));

        $offer = $this->em->getRepository(Offer::class)->find($id);

        return $this->render('offer/details.html.twig', [
            'offer' => $offer,
            'idOffer' => $id
        ]);
    }

    #[Route('/offres/delete/{id}', name: 'delete_offer')]
    public function deleteOffer(Request $request){

        $offerId = intval($request->get('id'));

        $offer = $this->em->getRepository(Offer::class)->find($offerId);

        $this->em->remove($offer);
        $this->em->flush();

        return $this->redirectToRoute('show_offer');

    }


    #[Route('/offres', name: 'show_offer')]
    public function showOffer(): Response
    {

        $offers = $this->em->getRepository(Offer::class)->findAll();

        return $this->render('offer/show.html.twig', [
            'offers' => $offers
        ]);
    }
}

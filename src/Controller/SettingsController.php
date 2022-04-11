<?php

namespace App\Controller;

use App\Form\UpdatePasswordFormType;
use App\Service\AuthService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class SettingsController extends AbstractController
{

    private AuthService $authService;
    private EntityManagerInterface $em;

    public function __construct(
        EntityManagerInterface $em,
        AuthService $authService
    )
    {
        $this->em = $em;
        $this->authService = $authService;
    }

    #[Route('/settings', name: 'app_settings')]
    public function index(Request $request, UserPasswordHasherInterface $userPasswordHasher): Response
    {

        $user = $this->authService->isAuthenticatedUser();
        $error = null;
        $success = null;

        $form = $this->createForm(UpdatePasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // On vérifie que le mot de passe actuel est valide
            if($userPasswordHasher->isPasswordValid($user, $form->getData()['actualPassword'])){

                // Maintenant qu'on est sûr que le mot de passe correspond avec l'utilisateur, on peut modifier son pwd
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('newPassword')->getData()
                    )
                );

                $this->em->persist($user);
                $this->em->flush();

                $success = 'Votre mot de passe vient d\'être modifié avec succès !';

                
            } else {
                // On affiche l'erreur pour prévenir l'utilisateur que son mot de passe n'est pas valide.
                $error = "Le mot de passe saisi, n'est pas valide.";
            }
        }

        // Les mots de passe ne correspond pas
        if ($form->isSubmitted() && !$form->isValid()) {
            $error = 'Les mots de passe ne correspondent pas.';
        }

        return $this->render('settings/settings.html.twig', [
            'form' => $form->createView(),
            'error' => $error,
            'success' => $success
        ]);
    }
}

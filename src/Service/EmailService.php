<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Security;

class EmailService
{

    private $em;

    public function __construct(
        EntityManagerInterface $em,
        Security $security,
        RouterInterface $router,
        TransportInterface $mailer)
    {
        $this->em = $em;
        $this->security = $security;
        $this->router = $router;
        $this->mailer = $mailer;

    }


    public function createAccount($user, $password, ){
        $email = (new TemplatedEmail())
            ->from(new Address('madjid@supinfo.com', 'Création de votre compte Madjid Booster'))
            ->to($user->getEmail())
            ->subject('Création de votre compte Madjid Booster')
            ->htmlTemplate('student/emailCreateAccount.html.twig')
            ->context([
                'password' => $password,
            ]);

        $this->mailer->send($email);
    }



}
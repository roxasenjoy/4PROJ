<?php

namespace App\EventSubscriber;

use App\Entity\User;
use JetBrains\PhpStorm\NoReturn;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class JWTSubscriber implements EventSubscriberInterface
{
    #[NoReturn] public function onLexikJwtAuthenticationOnJwtCreated(JWTCreatedEvent $event)
    {

        $data = $event->getData();
        $user = $event->getUser();

        if($user instanceof User){
            $data['username'] = $event->getUser()->getUsername();
            $event->setData($data);
        }


    }

    public static function getSubscribedEvents()
    {

        return [
            'lexik_jwt_authentication.on_jwt_created' => 'onLexikJwtAuthenticationOnJwtCreated',
        ];
    }
}

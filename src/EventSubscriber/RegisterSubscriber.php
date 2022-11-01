<?php

/**
 * Created by PhpStorm.
 * User: SAM Johnny
 * Date: 19/10/2022
 * Time: 21:41
 */

namespace App\EventSubscriber;

use App\Entity\User;
use App\Event\RegisterEvent;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class RegisterSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private TokenGeneratorInterface $tokenGenerator,
        private UserPasswordHasherInterface $passwordHasher
    )
    {
    }

    #[ArrayShape([RegisterEvent::NAME => "string"])]
    public static function getSubscribedEvents(): array
    {
        return [
            RegisterEvent::NAME => "onSetter",
        ];
    }

    public function onSetter(RegisterEvent $event): void
    {

        if ($event->getData() instanceof User) {

            /** @var User $user */
            $user = $event->getData();

            $user->setRegistrationToken($this->tokenGenerator->generateToken());
            $user->setPassword($this->passwordHasher->hashPassword(
                $user,
                $event->getForm()->get('password')->getData()));
        }
    }
}
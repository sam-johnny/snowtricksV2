<?php

/**
 * Created by PhpStorm.
 * User: SAM Johnny
 * Date: 19/10/2022
 * Time: 21:41
 */

namespace App\EventSubscriber;

use App\Entity\User;
use App\Event\ForgotPasswordEvent;
use App\Event\RegisterEvent;
use App\Handler\Message\UserNotificationForgotPasswordMessage;
use App\Repository\UserRepository;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class ForgotPasswordSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private TokenGeneratorInterface $tokenGenerator,
        private UserRepository $userRepository,
        private MessageBusInterface $messageBus)
    {
    }

    #[ArrayShape([ForgotPasswordEvent::NAME => "string"])]
    public static function getSubscribedEvents(): array
    {
        return [
            ForgotPasswordEvent::NAME => "onSetter",
        ];
    }

    public function onSetter(ForgotPasswordEvent $event): void
    {
            $form = $event->getForm();

            $user = $this->userRepository->findOneBy(['email' => $form['email']->getData()]);

            $user->setForgotPasswordToken($this->tokenGenerator->generateToken())
                ->setForgotPasswordTokenRequestedAt(new \DateTimeImmutable('now'))
                ->setForgotPasswordTokenMustBeVerifiedBefore(new \DateTimeImmutable('+15 minutes'));

            $this->messageBus->dispatch(new UserNotificationForgotPasswordMessage($user->getId()));
        }
}
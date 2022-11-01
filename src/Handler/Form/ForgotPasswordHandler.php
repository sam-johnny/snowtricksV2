<?php

/**
 * Created by PhpStorm.
 * User: SAM Johnny
 * Date: 16/09/2022
 * Time: 22:17
 */

namespace App\Handler\Form;

use App\Event\ForgotPasswordEvent;
use App\Form\ForgotPasswordType;
use App\Form\RegistrationFormType;
use App\Handler\Message\UserNotificationForgotPasswordMessage;
use App\Handler\Message\UserNotificationRegisterMessage;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Messenger\MessageBusInterface;

class ForgotPasswordHandler extends AbstractHandler
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    /**
     * @return string
     */
    #[Pure] protected function getFormType(): string
    {
        return ForgotPasswordType::class;
    }

    /**
     * @param
     * @return void
     */
    protected function process($data): void
    {
        $this->entityManager->flush();
    }

    #[Pure] protected function getEvent($data, $form): ?object
    {
        return new ForgotPasswordEvent($data, $form);
    }

    protected function getEventName(): ?string
    {
        return ForgotPasswordEvent::NAME;
    }

}
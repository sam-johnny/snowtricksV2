<?php

/**
 * Created by PhpStorm.
 * User: SAM Johnny
 * Date: 16/09/2022
 * Time: 22:17
 */

namespace App\Handler\Form;

use App\Event\ForgotPasswordEvent;
use App\Event\RegisterEvent;
use App\Form\RegistrationFormType;
use App\Handler\Message\UserNotificationRegisterMessage;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Messenger\MessageBusInterface;

class RegisterHandler extends AbstractHandler
{
    /**
     * @param EntityManagerInterface $entityManager
     * @param MessageBusInterface $messageBus
     */
    public function __construct(
        private EntityManagerInterface $entityManager,
        private MessageBusInterface $messageBus
    )
    {
    }

    /**
     * @inheritDoc
     */
    #[Pure] protected function getFormType(): string
    {
        return RegistrationFormType::class;
    }

    /**
     * @param
     * @return void
     */
    protected function process($data): void
    {
        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }

    #[Pure] protected function getEvent($data, $form): ?object
    {
        return new RegisterEvent($data, $form);
    }

    protected function getEventName(): ?string
    {
        return RegisterEvent::NAME;
    }

}
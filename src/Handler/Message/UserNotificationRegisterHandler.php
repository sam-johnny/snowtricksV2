<?php

/**
 * Created by PhpStorm.
 * User: SAM Johnny
 * Date: 24/10/2022
 * Time: 09:09
 */

namespace App\Handler\Message;

use App\Entity\User;
use App\Service\UserNotifierRegisterService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class UserNotificationRegisterHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserNotifierRegisterService $notifierRegisterService
    ){}

    /**
     * @throws TransportExceptionInterface
     */
    public function __invoke(UserNotificationRegisterMessage $message)
    {
        $user = $this->entityManager->find(User::class, $message->getUserId());

        if ($user !== null && is_a($message, "UserNotificationRegisterMessage")) {
            $this->notifierRegisterService->notify($user);
        }
    }
}


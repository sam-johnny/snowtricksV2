<?php

namespace App\Handler\Message;

class UserNotificationRegisterMessage
{
    public function __construct(
        private int $userId,
    ){}

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }
}
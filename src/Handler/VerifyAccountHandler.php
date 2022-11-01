<?php
declare(strict_types=1);

namespace App\Handler;

use App\Entity\User;
use Symfony\Component\Finder\Exception\AccessDeniedException;

class VerifyAccountHandler
{
    public function setter(User $user, string $token, $isNotRequestedInTime)
    {
        if ($user->getRegistrationToken() === null || ($user->getRegistrationToken()) !== $token ||
            ($isNotRequestedInTime())) {
            throw new AccessDeniedException();
        }

        $user->setIsVerified(true)
            ->setAccountVerifiedAt(new \DateTimeImmutable('now'))
            ->setRegistrationToken(null);
    }
}
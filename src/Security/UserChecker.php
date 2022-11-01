<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user)
    {
        return;
    }

    public function checkPostAuth(UserInterface $user)
    {
        if ($user instanceof User && !$user->getIsVerified()) {
            throw new CustomUserMessageAccountStatusException("Votre compte n'est pas actif, veuillez consulter vos e-mails pour l'activer avant le 
            {$user->getAccountMustBeVerifiedBefore()->format('d/m/Y à H\hi')}");
        }
    }
}
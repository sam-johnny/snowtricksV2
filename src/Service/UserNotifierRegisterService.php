<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

class UserNotifierRegisterService
{
    public function __construct(private MailerInterface $mailer)
    {
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function notify(User $user): void
    {
        $email = (new TemplatedEmail())
            ->from('noreply@snowtricks.com')
            ->to($user->getEmail())
            ->subject("Vérification de votre adresse e-mail pour activer votre compte utilisateur")
            ->htmlTemplate("registration/register_confirmation_email.html.twig")
            ->context([
                'userID' => $user->getId(),
                'registrationToken' => $user->getRegistrationToken(),
                'tokenLifeTime' => $user->getAccountMustBeVerifiedBefore()->format('d/m/Y à H:i')
            ]);

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $mailerException) {
            /** @var TransportExceptionInterface $mailerException */
            throw $mailerException;
        }
    }
}

<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

class UserNotifierForgetPasswordService
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
            ->subject("Modification de votre mot de passe")
            ->htmlTemplate("forgot_password/forgot_password_email.html.twig")
            ->context([
                'user' => $user
            ]);

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $mailerException) {
            /** @var TransportExceptionInterface $mailerException */
            throw $mailerException;
        }
    }
}

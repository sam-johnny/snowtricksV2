<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ForgotPasswordType;
use App\Form\ResetPasswordType;
use App\Handler\Form\ForgotPasswordHandler;
use App\Handler\Message\UserNotificationForgotPasswordMessage;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class ForgotPasswordController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private UserRepository $userRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserRepository         $userRepository)
    {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
    }


    #[Route('/forgot-password', name: 'forgot_password', methods: ['GET', 'POST'])]
    public function sendRecoveryLink(
        Request               $request,
        ForgotPasswordHandler $forgotPasswordHandler,
    ): Response
    {
        if ($forgotPasswordHandler->handle($request, null)) {

            $this->addFlash('success', 'Un email vous a été envoyé pour redéfinir votre mot de passe.');

            return $this->redirectToRoute('login');
        }
        return $this->render('forgot_password/forgot_password_step_1.html.twig', [
            'forgotPasswordFormStep1' => $forgotPasswordHandler->createView(),
        ]);
    }

    #[Route('/forgot-password/{id}/{token}', name: 'app_retrieve_credentials', methods: ['GET'])]
    public function retrieveCredentialsFromTheURL(
        string           $token,
        User             $user,
        SessionInterface $session
    ): RedirectResponse
    {
        $session->set('Reset-Password-Token-URL', $token);

        $session->set('Reset-Password-User-Email', $user->getEmail());

        return $this->redirectToRoute('app_reset_password');
    }

    #[Route('/reset-password', name: 'app_reset_password', methods: ['GET', 'POST'])]
    public function resetPassword(
        Request                     $request,
        UserPasswordHasherInterface $userPasswordHasher,
        SessionInterface            $session
    ): Response
    {
        [
            'token' => $token,
            'userEmail' => $userEmail
        ] = $this->getCredentialsFromSession($session);

        $user = $this->userRepository->findOneBy(['email' => $userEmail]);

        if (!$user) {
            return $this->redirectToRoute('login');
        }

        /** @var \DateTimeImmutable $forgotPasswordTokenMustBeVerifiedBefore */
        $forgotPasswordTokenMustBeVerifiedBefore = $user->getForgotPasswordTokenMustBeVerifiedBefore();

        if (($user->getForgotPasswordToken()) === null || ($user->getForgotPasswordToken() !== $token) ||
            ($this->isNotRequestedInTime($forgotPasswordTokenMustBeVerifiedBefore))) {
            return $this->redirectToRoute('app_reset_password');
        }

        $form = $this->createForm(ResetPasswordType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($userPasswordHasher->hashPassword($user, $form['password']->getData()));

            /* We clear the token to make it unusable. */
            $user->setForgotPasswordToken(null)
                ->setForgotPasswordTokenVerifiedAt(new \DateTimeImmutable('now'));

            $this->entityManager->flush();

            $this->removeCredentialsFromSession($session);

            $this->addFlash('success', 'Votre mot de passe a été modifié, vous pouvez à présent vous connecter.');

            return $this->redirectToRoute('app.home');
        }

        return $this->render('forgot_password/forgot_password_step_2.html.twig', [
            'forgotPasswordFormStep2' => $form->createView(),
            'passwordMustBeModifiedBefore' => $this->passwordMustBeModifiedBefore($user)
        ]);
    }

    /**
     * Get the user ID and token from the session.
     *
     * @param SessionInterface $session
     * @return array
     */
    #[ArrayShape(['token' => "mixed", 'userEmail' => "mixed"])]
    private function getCredentialsFromSession(SessionInterface $session): array
    {
        return [
            'token' => $session->get('Reset-Password-Token-URL'),
            'userEmail' => $session->get('Reset-Password-User-Email')
        ];
    }


    /**
     * Validates or not the fact that the link was clicked in the alloted time.
     *
     * @param \DateTimeImmutable $forgotPasswordTokenMustBeVerifiedBefore
     * @return bool
     */
    private function isNotRequestedInTime(\DateTimeImmutable $forgotPasswordTokenMustBeVerifiedBefore): bool
    {
        return (new \DateTimeImmutable('now') > $forgotPasswordTokenMustBeVerifiedBefore);
    }

    /**
     * Removes the user ID and token from the session.
     *
     * @param SessionInterface $session
     * @return void
     */
    private function removeCredentialsFromSession(SessionInterface $session): void
    {
        $session->remove('Reset-Password-Token-URL');

        $session->remove('Reset-Password-User-Email');
    }

    /**
     * Returns the time before which the password must be changed.
     *
     * @param User $user
     * @return string The time in this format: 12h00
     */
    private function passwordMustBeModifiedBefore(User $user): string
    {

        /** @var \DateTimeImmutable $passwordMustBeModifiedBefore */
        $passwordMustBeModifiedBefore = $user->getForgotPasswordTokenMustBeVerifiedBefore();

        return $passwordMustBeModifiedBefore->format('H\hi');
    }
}

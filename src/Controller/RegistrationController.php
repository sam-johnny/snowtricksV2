<?php

namespace App\Controller;

use App\Entity\User;
use App\Handler\Form\RegisterHandler;
use App\Handler\VerifyAccountHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/register', name: 'app_register')]
    public function register(
        Request               $request,
        RegisterHandler $registerHandler
    ): Response
    {
        $user = new User();

        if ($registerHandler->handle($request, $user)) {

            $this->addFlash('success', "Votre compte utilisateur à bien été créé, vérifiez vos e-mail pour l'activer.");

            return $this->redirectToRoute('login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $registerHandler->createView(),
        ]);
    }

    #[Route('/{id}/{token}', name: 'app_verify_account', methods: ['GET'])]
    public function verifyAccount(
        User   $user,
        string $token,
        VerifyAccountHandler $verifyAccountHandler
    ): Response
    {
        $verifyAccountHandler->setter(
            $user,
            $token,
            $this->isNotRequestedInTime($user->getAccountMustBeVerifiedBefore())
        );

        $this->entityManager->flush();

        $this->addFlash('success', 'Votre compte utilisateur est activé, vous pouvez vous connecter !');

        return $this->redirectToRoute('login');

    }

    private function isNotRequestedInTime(\DateTimeImmutable $accountMustBeVerifiedBefore): bool
    {
        return (new \DateTimeImmutable('now') > $accountMustBeVerifiedBefore);
    }
}





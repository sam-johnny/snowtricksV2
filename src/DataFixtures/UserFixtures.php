<?php

/**
 * Created by PhpStorm.
 * User: SAM Johnny
 * Date: 07/10/2022
 * Time: 11:53
 */

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {}

    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 5; ++$i) {
            $user = new User();
            $user->setUsername(sprintf('user%d', $i));
            $user->setEmail(sprintf('user%d@email.com', $i));
            $user->setPassword($this->passwordHasher->hashPassword($user, 'password'));
            $user->setIsVerified(1);
            $manager->persist($user);
        }

        for ($i = 6; $i <= 6; ++$i) {
            $user = new User();
            $user->setUsername(sprintf('user%d', $i));
            $user->setEmail(sprintf('user%d@email.com', $i));
            $user->setPassword($this->passwordHasher->hashPassword($user, 'password'));
            $user->setIsVerified(0);
            $manager->persist($user);
        }

        $manager->flush();
    }
}
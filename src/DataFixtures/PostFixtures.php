<?php

/**
 * Created by PhpStorm.
 * User: SAM Johnny
 * Date: 07/10/2022
 * Time: 14:46
 */

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class PostFixtures extends Fixture implements DependentFixtureInterface
{

    public function getDependencies(): array
    {
        return [
            CategoryFixtures::class,
            UserFixtures::class
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        /**
         * @var array<array-key, Category> $categories
         */
        $categories = $manager->getRepository(Category::class)->findAll();

        /**
         * @var array<array-key, User> $users
         */
        $users = $manager->getRepository(User::class)->findAll();

        foreach ($users as $user) {
            foreach ($categories as $category) {
                for ($i = 1; $i <= 5; ++$i) {
                    $post = new Post();
                    $post->setCategory($category);
                    $post->setUser($user);
                    $post->setTitle($faker->words(3, true));
                    $post->setContent($faker->paragraphs(2, true));

                    $manager->persist($post);

                    for ($i = 1; $i <= 20; ++$i) {
                        $comment = new Comment();
                        $comment->setUser($user);
                        $comment->setPost($post);
                        $comment->setContent($faker->words(3, true));

                        $manager->persist($comment);
                    }
                }
            }
        }

        $manager->flush();
    }
}
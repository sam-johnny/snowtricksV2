<?php

/**
 * Created by PhpStorm.
 * User: SAM Johnny
 * Date: 07/10/2022
 * Time: 12:14
 */

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CategoryFixtures extends Fixture
{

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        for ($i = 1; $i <= 5; ++$i)
            {
                $category = new Category();
                $category->setName($faker->word());
                $manager->persist($category);
            }

        $manager->flush();
    }
}
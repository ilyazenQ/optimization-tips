<?php

namespace App\DataFixtures;

use App\Entity\Place;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        $firstPlace = $manager->getRepository(Place::class)->findBy([],['id' =>'asc'],1)[0];

        // Create users
        for ($i = 0; $i < 5000; $i++) {
            $user = new User();
            $user->setName($faker->name);
            $user->setIsActive($faker->boolean(50));

            for ($j = 0; $j < rand(1, 4); $j++) {
                $place = $manager->getRepository(Place::class)->find(rand($firstPlace->getId(), $firstPlace->getId()+9999));
                $user->addPlace($place);
            }

            $manager->persist($user);
        }

        $manager->flush();
        $manager->clear();
    }
}
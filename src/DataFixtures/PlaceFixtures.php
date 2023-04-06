<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Place;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class PlaceFixtures extends Fixture implements FixtureGroupInterface
{
    public static function getGroups(): array
     {
        return ['place'];
     }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        $firstCategory = $manager->getRepository(Category::class)->findBy([],['id' =>'asc'],1)[0];
        // Create places
        $count     = 1;
        $batchSize = 100;
        for ($i = 0; $i < 10000; $i++) {
            $place = new Place();
            $place->setTitle($faker->company);

            $category = $manager->getRepository(Category::class)->find(rand($firstCategory->getId(), $firstCategory->getId()+19));
            $place->setCategory($category);
            
            $manager->persist($place);

            if ($count % $batchSize === 0) {
                $manager->flush();
                $manager->clear();
                gc_collect_cycles();
                $count = 1;
            } else {
                $count++;
            }
        }

        $manager->flush();
        $manager->clear();
    }
}
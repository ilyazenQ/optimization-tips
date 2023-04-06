<?php 

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CategoryFixtures extends Fixture implements FixtureGroupInterface
{
    public static function getGroups(): array
     {
         return ['category'];
     }
    
     public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        // Create categories
        for ($i = 0; $i < 20; $i++) {
            $category = new Category();
            $category->setTitle($faker->word);
            $manager->persist($category);
        }

        $manager->flush();
        $manager->clear();

    }

    /**
     */
    public function __construct() {
    }
}

<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $categoryFixtures = new CategoryFixtures();
        $categoryFixtures->load($manager);

        $placeFixtures = new PlaceFixtures();
        $placeFixtures->load($manager);

        $userFixtures = new UserFixtures();
        $userFixtures->load($manager);
    }

}

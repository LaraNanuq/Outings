<?php

namespace App\DataFixtures;

use App\Entity\City;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CityFixtures extends Fixture {

    public function load(ObjectManager $manager) {
        $city1 = new City();
        $city1->setName('Saint Herblain');
        $city1->setPostalCode('44800');
        $manager->persist($city1);
        $this->addReference(self::class . '1', $city1);

        $city2 = new City();
        $city2->setName('Herblay');
        $city2->setPostalCode('95220');
        $manager->persist($city2);
        $this->addReference(self::class . '2', $city2);

        $city3 = new City();
        $city3->setName('Cherbourg');
        $city3->setPostalCode('50100');
        $manager->persist($city3);
        $this->addReference(self::class . '3', $city3);

        $manager->flush();
    }
}

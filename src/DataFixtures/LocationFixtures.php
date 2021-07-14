<?php

namespace App\DataFixtures;

use App\Entity\Location;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LocationFixtures extends Fixture implements DependentFixtureInterface {

    public function load(ObjectManager $manager) {
        $location1 = new Location();
        $location1->setName('Le CAP');
        $location1->setLatitude('1.23456789');
        $location1->setLongitude('-9.87654321');
        $location1->setStreet('Rue des compagnons');
        $location1->setCity($this->getReference(CityFixtures::class . '1'));
        $manager->persist($location1);
        $this->addReference(self::class . '1', $location1);

        $manager->flush();
    }

    public function getDependencies(): array {
        return [CityFixtures::class];
    }
}

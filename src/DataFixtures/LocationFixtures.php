<?php

namespace App\DataFixtures;

use App\Entity\Location;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * @author Marin Taverniers
 */
class LocationFixtures extends Fixture implements DependentFixtureInterface {

    public function load(ObjectManager $manager) {
        $location1 = new Location();
        $location1->setName('Le CAP');
        $location1->setLatitude('1.2345678');
        $location1->setLongitude('-9.8765432');
        $location1->setStreet('1 Rue des compagnons');
        $location1->setCity($this->getReference(CityFixtures::class . '1'));
        $manager->persist($location1);
        $this->addReference(self::class . '1', $location1);

        $location2 = new Location();
        $location2->setName('Pont neuf');
        $location2->setLatitude('52.4821521');
        $location2->setLongitude('45.9713254');
        $location2->setStreet('28 Rue du moulin');
        $location2->setCity($this->getReference(CityFixtures::class . '2'));
        $manager->persist($location2);
        $this->addReference(self::class . '2', $location2);

        $location3 = new Location();
        $location3->setName('Chez lulu');
        $location3->setLatitude('-2.4137584');
        $location3->setLongitude('12.4315276');
        $location3->setStreet('8 Place de la mairie');
        $location3->setCity($this->getReference(CityFixtures::class . '3'));
        $manager->persist($location3);
        $this->addReference(self::class . '3', $location3);

        $manager->flush();
    }

    public function getDependencies(): array {
        return [CityFixtures::class];
    }
}

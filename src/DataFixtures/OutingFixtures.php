<?php

namespace App\DataFixtures;

use App\Entity\Location;
use App\Entity\Outing;
use App\Entity\User;
use App\Repository\OutingStateRepository;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * @author Marin Taverniers
 */
class OutingFixtures extends Fixture implements DependentFixtureInterface {
    private OutingStateRepository $outingStateRepository;

    public function __construct(OutingStateRepository $outingStateRepository) {
        $this->outingStateRepository = $outingStateRepository;
    }

    public function load(ObjectManager $manager) {
        $outing1 = new Outing();
        $outing1->setName('Ski alpin');
        $outing1->setDescription("Le ski alpin c'est chouette.");
        $outing1->setDate(new DateTime('now +1 year'));
        $outing1->setDuration(240);
        $outing1->setRegistrationClosingDate(new DateTime('now +6 month'));
        $outing1->setMaxRegistrants(100);
        $user = $this->getReference(UserFixtures::class . '2');
        if ($user instanceof User) {
            $outing1->setOrganizer($user);
        }
        $outing1->setCampus($user->getCampus());
        $location = $this->getReference(LocationFixtures::class . '1');
        if ($location instanceof Location) {
            $outing1->setLocation($location);
        }
        $outing1->setState($this->outingStateRepository->findOneBy(['label' => 'OPEN']));
        $manager->persist($outing1);

        $outing2 = new Outing();
        $outing2->setName('Randonnée');
        $outing2->setDescription("La randonnée c'est chouette.");
        $outing2->setDate(new DateTime('now -5 day'));
        $outing2->setDuration(120);
        $outing2->setRegistrationClosingDate(new DateTime('now -10 day'));
        $outing2->setMaxRegistrants(50);
        $user = $this->getReference(UserFixtures::class . '3');
        if ($user instanceof User) {
            $outing2->setOrganizer($user);
        }
        $outing2->setCampus($user->getCampus());
        $location = $this->getReference(LocationFixtures::class . '2');
        if ($location instanceof Location) {
            $outing2->setLocation($location);
        }
        $outing2->setState($this->outingStateRepository->findOneBy(['label' => 'FINISHED']));
        $manager->persist($outing2);

        $outing3 = new Outing();
        $outing3->setName('Cyclisme');
        $outing3->setDescription("Le cyclisme c'est chouette.");
        $outing3->setDate(new DateTime('now +6 month'));
        $outing3->setDuration(60);
        $outing3->setRegistrationClosingDate(new DateTime('now +3 month'));
        $outing3->setMaxRegistrants(25);
        $user = $this->getReference(UserFixtures::class . '4');
        if ($user instanceof User) {
            $outing3->setOrganizer($user);
        }
        $outing3->setCampus($user->getCampus());
        $location = $this->getReference(LocationFixtures::class . '3');
        if ($location instanceof Location) {
            $outing3->setLocation($location);
        }
        $outing3->setState($this->outingStateRepository->findOneBy(['label' => 'DRAFT']));
        $manager->persist($outing3);

        $manager->flush();
    }

    public function getDependencies(): array {
        return [UserFixtures::class, LocationFixtures::class];
    }
}

<?php

namespace App\DataFixtures;

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
    private $outingStateRepository;

    public function __construct(OutingStateRepository $outingStateRepository) {
        $this->outingStateRepository = $outingStateRepository;
    }

    public function load(ObjectManager $manager) {
        $outing1 = new Outing();
        $outing1->setName('Randonnée');
        $outing1->setDescription("La randonnée c'est chouette.");
        $outing1->setDate(new DateTime('now +1 year'));
        $outing1->setDuration(240);
        $outing1->setRegistrationClosingDate(new DateTime('now +6 month'));
        $outing1->setMaxRegistrants(10);
        /* @var $user User */
        $user = $this->getReference(UserFixtures::class . '2');
        $outing1->setOrganizer($user);
        $outing1->setCampus($user->getCampus());
        $outing1->setLocation($this->getReference(LocationFixtures::class . '1'));
        $outing1->setState($this->outingStateRepository->findOneBy(['label' => 'OPEN']));
        $manager->persist($outing1);

        $manager->flush();
    }

    public function getDependencies(): array {
        return [UserFixtures::class, LocationFixtures::class];
    }
}

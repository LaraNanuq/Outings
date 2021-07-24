<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @author Marin Taverniers
 */
class UserFixtures extends Fixture implements DependentFixtureInterface {
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher) {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager) {
        $admin = new User();
        $admin->setAlias('admin');
        $admin->setLastName('Doe');
        $admin->setFirstName('John');
        $admin->setEmail('admin@outings.com');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin'));
        $admin->setRoles(['ROLE_ADMIN']);
        $campus = $this->getReference(CampusFixtures::class . '1');
        if ($campus instanceof Campus) {
            $admin->setCampus($campus);
        }
        $manager->persist($admin);
        $this->addReference(self::class . '1', $admin);

        $user1 = new User();
        $user1->setAlias('user1');
        $user1->setLastName('Legrand');
        $user1->setFirstName('Michel');
        $user1->setEmail('user1@gmail.com');
        $user1->setPassword($this->passwordHasher->hashPassword($user1, 'user1'));
        $user1->setRoles(['ROLE_USER']);
        $campus = $this->getReference(CampusFixtures::class . '2');
        if ($campus instanceof Campus) {
            $user1->setCampus($campus);
        }
        $manager->persist($user1);
        $this->addReference(self::class . '2', $user1);

        $user2 = new User();
        $user2->setAlias('user2');
        $user2->setLastName('Petit');
        $user2->setFirstName('Louis');
        $user2->setEmail('user2@gmail.com');
        $user2->setPassword($this->passwordHasher->hashPassword($user2, 'user2'));
        $user2->setRoles(['ROLE_USER']);
        $campus = $this->getReference(CampusFixtures::class . '3');
        if ($campus instanceof Campus) {
            $user2->setCampus($campus);
        }
        $manager->persist($user2);
        $this->addReference(self::class . '3', $user2);

        $user3 = new User();
        $user3->setAlias('user3');
        $user3->setLastName('Times');
        $user3->setFirstName('Vincent');
        $user3->setEmail('user3@gmail.com');
        $user3->setPassword($this->passwordHasher->hashPassword($user3, 'user3'));
        $user3->setRoles(['ROLE_USER']);
        $campus = $this->getReference(CampusFixtures::class . '1');
        if ($campus instanceof Campus) {
            $user3->setCampus($campus);
        }
        $manager->persist($user3);
        $this->addReference(self::class . '4', $user3);

        $disabled = new User();
        $disabled->setAlias('disabled');
        $disabled->setLastName('Dupont');
        $disabled->setFirstName('Jean');
        $disabled->setEmail('disabled@gmail.com');
        $disabled->setPassword($this->passwordHasher->hashPassword($disabled, 'disabled'));
        $disabled->setRoles(['ROLE_USER_DISABLED']);
        $campus = $this->getReference(CampusFixtures::class . '2');
        if ($campus instanceof Campus) {
            $disabled->setCampus($campus);
        }
        $manager->persist($disabled);
        $this->addReference(self::class . '5', $disabled);

        $manager->flush();
    }

    public function getDependencies(): array {
        return [CampusFixtures::class];
    }
}

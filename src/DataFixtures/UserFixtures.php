<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @author Marin Taverniers
 */
class UserFixtures extends Fixture implements DependentFixtureInterface {
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher) {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager) {
        $admin = new User();
        $admin->setAlias('admin');
        $admin->setLastName('Dupont');
        $admin->setFirstName('Jean');
        $admin->setEmail('admin@outings.com');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin'));
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setCampus($this->getReference(CampusFixtures::class . '1'));
        $manager->persist($admin);
        $this->addReference(self::class . '1', $admin);

        $user = new User();
        $user->setAlias('user');
        $user->setLastName('Doe');
        $user->setFirstName('John');
        $user->setEmail('user@gmail.com');
        $user->setPassword($this->passwordHasher->hashPassword($user, 'user'));
        $user->setRoles(['ROLE_USER']);
        $user->setCampus($this->getReference(CampusFixtures::class . '2'));
        $manager->persist($user);
        $this->addReference(self::class . '2', $user);

        $disabled = new User();
        $disabled->setAlias('disabled');
        $disabled->setLastName('Disabled');
        $disabled->setFirstName('Jane');
        $disabled->setEmail('disabled@gmail.com');
        $disabled->setPassword($this->passwordHasher->hashPassword($disabled, 'disabled'));
        $disabled->setRoles(['ROLE_USER_DISABLED']);
        $disabled->setCampus($this->getReference(CampusFixtures::class . '3'));
        $manager->persist($disabled);
        $this->addReference(self::class . '3', $disabled);

        $manager->flush();
    }

    public function getDependencies(): array {
        return [CampusFixtures::class];
    }
}

<?php

namespace App\Repository;

use App\Entity\Campus;
use App\Entity\Outing;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Outing|null find($id, $lockMode = null, $lockVersion = null)
 * @method Outing|null findOneBy(array $criteria, array $orderBy = null)
 * @method Outing[]    findAll()
 * @method Outing[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * 
 * @author Marin Taverniers
 */
class OutingRepository extends ServiceEntityRepository {

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Outing::class);
    }

    public function findByFilters(
        Campus $campus,
        string $name,
        DateTimeInterface $minDate,
        DateTimeInterface $maxDate,
        bool $isUserOrganizer,
        bool $isUserRegistrant,
        bool $isUserNotRegistrant,
        bool $isFinished
    ) {
        
    }
}

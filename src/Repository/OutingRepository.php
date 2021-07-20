<?php

namespace App\Repository;

use App\Entity\Outing;
use App\Entity\User;
use App\Form\SearchOutingFilter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
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
    private const ITEMS_PER_PAGE = 100;

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Outing::class);
    }

    public function findWithSearchFilter(SearchOutingFilter $searchFilter, User $relatedUser) {
        $builder = $this
            ->createQueryBuilder('o');
        if ($searchFilter->getCampus()) {
            $builder = $builder
                //->join('o.campus', 'c')
                ->andWhere('o.campus = :campus')
                ->setParameter('campus', $searchFilter->getCampus());
        }
        if ($searchFilter->getName()) {
            $builder = $builder
                ->andWhere('o.name LIKE :name')
                ->setParameter('name', "%{$searchFilter->getName()}%");
        }
        if ($searchFilter->getMinDate()) {
            $builder = $builder
                ->andWhere('o.date >= :minDate')
                ->setParameter('minDate', $searchFilter->getMinDate());
        }
        if ($searchFilter->getMaxDate()) {
            $builder = $builder
                ->andWhere('o.date <= :maxDate')
                ->setParameter('maxDate', $searchFilter->getMaxDate());
        }
        if ($searchFilter->isUserOrganizer()) {
            $builder = $builder
                ->andWhere('o.organizer = :organizer')
                ->setParameter('organizer', $relatedUser);
        }
        if ($searchFilter->isUserRegistrant()) {
            $builder = $builder
                ->andWhere(':registrant MEMBER OF o.registrants')
                ->setParameter('registrant', $relatedUser);
        }
        if ($searchFilter->isUserNotRegistrant()) {
            $builder = $builder
                ->andWhere(':excludedRegistrant NOT MEMBER OF o.registrants')
                ->setParameter('excludedRegistrant', $relatedUser);
        }
        $builder
            ->join('o.state', 's');
        if ($searchFilter->isFinished()) {
            $builder = $builder
                ->andWhere("s.label = 'FINISHED'");
        } else {
            $builder
                ->andWhere("s.label != 'ARCHIVED'");
        }
        $builder
            ->addOrderBy('o.date', 'ASC');
        
        $query = $builder
            ->getQuery()
            ->setFirstResult(($searchFilter->getPage() - 1) * self::ITEMS_PER_PAGE)
            ->setMaxResults(self::ITEMS_PER_PAGE);
        return new Paginator($query);
    }
}

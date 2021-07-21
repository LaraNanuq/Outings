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

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Outing::class);
    }

    public function findWithSearchFilter(SearchOutingFilter $searchFilter, User $relatedUser) {
        $builder = $this
            ->createQueryBuilder('o')
            ->addSelect('u')
            ->addSelect('s')
            ->addSelect('r')
            ->join('o.organizer', 'u')
            ->join('o.state', 's')
            ->leftJoin('o.registrants', 'r');

        // Input fields
        if ($searchFilter->getCampus()) {
            $builder
                ->addSelect('ca')
                ->join('o.campus', 'ca')
                ->andWhere('o.campus = :campus')
                ->setParameter('campus', $searchFilter->getCampus());
        }
        if ($searchFilter->getName()) {
            $builder
                ->andWhere('o.name LIKE :name')
                ->setParameter('name', "%{$searchFilter->getName()}%");
        }
        if ($searchFilter->getMinDate()) {
            $builder
                ->andWhere('o.date >= :minDate')
                ->setParameter('minDate', $searchFilter->getMinDate());
        }
        if ($searchFilter->getMaxDate()) {
            $builder
                ->andWhere('o.date <= :maxDate')
                ->setParameter('maxDate', $searchFilter->getMaxDate());
        }

        // Checkboxes
        if (!($searchFilter->isUserOrganizer() && $searchFilter->isUserRegistrant() && $searchFilter->isUserNotRegistrant())) {
            if ($searchFilter->isUserOrganizer()) {
                $builder->andWhere('o.organizer = :organizer');
            } else {
                $builder->andWhere('o.organizer != :organizer');
            }
            $builder->setParameter('organizer', $relatedUser);
            if ($searchFilter->isUserRegistrant() XOR $searchFilter->isUserNotRegistrant()) {
                if ($searchFilter->isUserRegistrant()) {
                    $part = ':registrant MEMBER OF o.registrants';
                }
                if ($searchFilter->isUserNotRegistrant()) {
                    $part = ':registrant NOT MEMBER OF o.registrants';
                }
                if ($searchFilter->isUserOrganizer()) {
                    $builder->orWhere($part);
                } else {
                    $builder->andWhere($part);
                }
                $builder->setParameter('registrant', $relatedUser);
            }
        }
        if ($searchFilter->isFinished()) {
            $builder->andWhere("UPPER(s.label) = 'FINISHED'");
        } else {
            $builder->andWhere("UPPER(s.label) != 'FINISHED'");
        }

        // Sorting
        $builder
            ->andWhere("UPPER(s.label) != 'ARCHIVED'")
            ->addOrderBy('o.date', 'ASC');

        // Without pagination
        $query = $builder->getQuery();
        if ((!$searchFilter->getPage()) || (!$searchFilter->getItemsPerPage())) {
            return $query->getResult();
        }

        // With pagination
        $query
            ->setFirstResult(($searchFilter->getPage() - 1) * $searchFilter->getItemsPerPage())
            ->setMaxResults($searchFilter->getItemsPerPage());
        return new Paginator($query);
    }
}

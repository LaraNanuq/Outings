<?php

namespace App\Repository;

use App\Entity\Outing;
use App\Entity\SearchOutingFilter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

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

    public function findWithSearchFilter(SearchOutingFilter $searchFilter, UserInterface $relatedUser) {
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
        if ($searchFilter->isUserOrganizer()) {

            // 'OR' clauses (do not use 'orWhere()')
            $orClause = ['o.organizer = :user'];
            if ($searchFilter->isUserRegistrant()) {
                $orClause[] = ':user MEMBER OF o.registrants';
            }
            if ($searchFilter->isUserNotRegistrant()) {
                $orClause[] = ':user NOT MEMBER OF o.registrants';
            }
            $builder->andWhere('(' . join(' OR ', $orClause) . ')');
        } else {

            // 'AND' clauses
            $builder->andWhere("o.organizer != :user");
            if (!$searchFilter->isUserRegistrant()) {
                $builder->andWhere(":user NOT MEMBER OF o.registrants");
            }
            if (!$searchFilter->isUserNotRegistrant()) {
                $builder->andWhere(":user MEMBER OF o.registrants");
            }
        }
        if ($searchFilter->isFinished()) {
            $builder->andWhere("UPPER(s.label) = 'FINISHED'");
        } else {
            $builder->andWhere("UPPER(s.label) != 'FINISHED'");
        }

        // State and order
        $builder
            ->andWhere("(UPPER(s.label) != 'DRAFT' OR o.organizer = :user)")
            ->andWhere("UPPER(s.label) != 'ARCHIVED'")
            ->addOrderBy('o.date', 'ASC');
        $builder->setParameter('user', $relatedUser);

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

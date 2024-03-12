<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Category>
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    public function store(Category $category, bool $flush = false): void
    {
        $this->getEntityManager()->persist($category);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Category $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return array<int, Category>
     */
    public function findAllOrdered(): array
    {
        // DQL query
        // $dql = 'SELECT c FROM App\Entity\Category as c ORDER BY c.name';

        $qb = $this->createQueryBuilder('c')
            ->addOrderBy('c.name', 'ASC');

        return $qb->getQuery()->getResult();
    }

    /**
     * @return array<int, Category>
     */
    public function search(string $term): array
    {
        $qb = $this->createQueryBuilder('c');

        $qb->addSelect('fc')
            ->leftJoin('c.fortuneCookies', 'fc')
            ->andWhere('c.name LIKE :term OR c.iconKey LIKE :term OR fc.fortune LIKE :term')
            ->setParameter('term', '%' . $term . '%')
            ->addOrderBy('c.name', 'ASC');

        return $qb->getQuery()->getResult();
    }

    public function findWithFortunesJoin(int $id): ?Category
    {
        $qb = $this->createQueryBuilder('c');

        $qb->addSelect('fc')
            ->leftJoin('c.fortuneCookies', 'fc')
            ->andWhere('c.id = :id')
            ->setParameter('id', $id);

        return $qb->getQuery()->getOneOrNullResult();
    }
}

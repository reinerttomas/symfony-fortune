<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Category;
use App\Repository\Query\CategoryOrderByName;
use App\Repository\Query\FortuneCookieJoinAndSelect;
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

        $qb = $this->createQueryBuilder('c');

        //        FortuneCookieJoinAndSelect::new($qb)
        //            ->build();
        CategoryOrderByName::new($qb)
            ->addOrder('DESC')
            ->build();

        return $qb->getQuery()->getResult();
    }

    /**
     * @return array<int, Category>
     */
    public function search(string $term): array
    {
        $qb = $this->createQueryBuilder('c');

        $terms = explode(' ', $term);

        FortuneCookieJoinAndSelect::new($qb)
            ->build()
            ->andWhere('c.name LIKE :term OR c.name IN (:terms) OR c.iconKey LIKE :term OR fc.fortune LIKE :term')
            ->setParameter('term', '%' . $term . '%')
            ->setParameter('terms', $terms)
            ->addOrderBy('c.name', 'ASC');

        return $qb->getQuery()->getResult();
    }

    public function findWithFortunesJoin(int $id): ?Category
    {
        $qb = $this->createQueryBuilder('c');

        FortuneCookieJoinAndSelect::new($qb)
            ->build()
            ->andWhere('c.id = :id')
            ->setParameter('id', $id);

        return $qb->getQuery()->getOneOrNullResult();
    }
}

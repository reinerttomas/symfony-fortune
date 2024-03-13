<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Category;
use App\Repository\Query\CategoryGroupByCategoryAndCountFortuneCookies;
use App\Repository\Query\CategoryJoinAndSelectFortuneCookie;
use App\Repository\Query\CategoryOrderByName;
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
     * @return Category[]
     */
    public function findAllOrdered(): array
    {
        // DQL query
        // $dql = 'SELECT c FROM App\Entity\Category as c ORDER BY c.name';

        $qb = $this->createQueryBuilder('c');

        CategoryGroupByCategoryAndCountFortuneCookies::create($qb)
            ->build();
        CategoryOrderByName::create($qb)
            ->addOrder('DESC')
            ->build();

        return $this->getCategoryWithFortuneCookiesTotal($qb->getQuery()->getResult()); // @phpstan-ignore-line
    }

    /**
     * @return Category[]
     */
    public function search(string $term): array
    {
        $qb = $this->createQueryBuilder('c');

        $terms = explode(' ', $term);
        CategoryGroupByCategoryAndCountFortuneCookies::create($qb)
            ->build();
        CategoryOrderByName::create($qb)
            ->addOrder('DESC')
            ->build();

        $qb->andWhere('c.name LIKE :term OR c.name IN (:terms) OR c.iconKey LIKE :term OR fc.fortune LIKE :term')
            ->setParameter('term', '%' . $term . '%')
            ->setParameter('terms', $terms);

        return $this->getCategoryWithFortuneCookiesTotal($qb->getQuery()->getResult()); // @phpstan-ignore-line
    }

    public function findWithFortunesJoin(int $id): ?Category
    {
        $qb = $this->createQueryBuilder('c');

        CategoryJoinAndSelectFortuneCookie::create($qb)
            ->build()
            ->andWhere('c.id = :id')
            ->orderBy('RAND()', 'ASC')
            ->setParameter('id', $id);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @param  array<int, array{ category: Category, fortuneCookiesTotal: int }>  $results
     * @return Category[]
     */
    private function getCategoryWithFortuneCookiesTotal(array $results): array
    {
        /** @var array<int, Category> $categories */
        $categories = [];

        foreach ($results as $result) {
            $categories[] = $result['category']->setFortuneCookiesTotal($result['fortuneCookiesTotal']);
        }

        return $categories;
    }
}

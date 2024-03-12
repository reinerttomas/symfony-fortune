<?php

declare(strict_types=1);

namespace App\Repository;

use App\Data\CategoryFortuneStats;
use App\Entity\Category;
use App\Entity\FortuneCookie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FortuneCookie>
 */
class FortuneCookieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FortuneCookie::class);
    }

    public function store(FortuneCookie $fortuneCookie, bool $flush = false): void
    {
        $this->getEntityManager()->persist($fortuneCookie);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(FortuneCookie $fortuneCookie, bool $flush = false): void
    {
        $this->getEntityManager()->remove($fortuneCookie);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function countNumberPrintedByCategory(Category $category): CategoryFortuneStats
    {
        $qb = $this->createQueryBuilder('fc');

        $qb
            ->select(sprintf(
                'NEW %s(
                    SUM(fc.numberPrinted),
                    AVG(fc.numberPrinted),
                    c.name
                )',
                CategoryFortuneStats::class,
            ))
            ->join('fc.category', 'c')
            ->andWhere('fc.category = :category')
            ->setParameter('category', $category);

        return $qb->getQuery()->getSingleResult();
    }
}

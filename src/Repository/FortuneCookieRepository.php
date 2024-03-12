<?php

declare(strict_types=1);

namespace App\Repository;

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
}

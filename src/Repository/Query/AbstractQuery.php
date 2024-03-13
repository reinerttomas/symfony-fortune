<?php

declare(strict_types=1);

namespace App\Repository\Query;

use Doctrine\ORM\QueryBuilder;

abstract class AbstractQuery implements Queryable
{
    public function __construct(protected QueryBuilder $qb)
    {
    }

    public static function create(QueryBuilder $qb): static
    {
        return new static($qb);
    }
}

<?php

declare(strict_types=1);

namespace App\Repository\Query;

use Doctrine\ORM\QueryBuilder;

final class CategoryJoinAndSelectFortuneCookie extends AbstractQuery
{
    public function build(): QueryBuilder
    {
        return $this->qb
            ->addSelect('fc')
            ->leftJoin('c.fortuneCookies', 'fc');
    }
}

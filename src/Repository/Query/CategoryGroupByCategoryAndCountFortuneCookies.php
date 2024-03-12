<?php

declare(strict_types=1);

namespace App\Repository\Query;

use Doctrine\ORM\QueryBuilder;

final class CategoryGroupByCategoryAndCountFortuneCookies extends AbstractQuery
{
    public function build(): QueryBuilder
    {
        return $this->qb
            ->select('c as category, COUNT(fc.id) as fortuneCookiesTotal')
            ->leftJoin('c.fortuneCookies', 'fc')
            ->addGroupBy('c.id');
    }
}

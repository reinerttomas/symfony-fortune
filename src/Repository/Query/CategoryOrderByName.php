<?php

declare(strict_types=1);

namespace App\Repository\Query;

use Doctrine\ORM\QueryBuilder;

class CategoryOrderByName extends AbstractQuery
{
    private string $order = 'ASC';

    public function addOrder(string $order): self
    {
        $this->order = $order;

        return $this;
    }

    public function build(): QueryBuilder
    {
        return $this->qb
            ->addOrderBy('c.name', $this->order);
    }
}

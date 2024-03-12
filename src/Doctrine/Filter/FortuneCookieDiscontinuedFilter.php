<?php

declare(strict_types=1);

namespace App\Doctrine\Filter;

use App\Entity\FortuneCookie;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

class FortuneCookieDiscontinuedFilter extends SQLFilter
{
    public function addFilterConstraint(ClassMetadata $targetEntity, string $targetTableAlias): string
    {
        if ($targetEntity->name !== FortuneCookie::class) {
            return '';
        }

        return sprintf('%s.discontinued = %s', $targetTableAlias, (int) $this->getParameter('discontinued'));
    }
}

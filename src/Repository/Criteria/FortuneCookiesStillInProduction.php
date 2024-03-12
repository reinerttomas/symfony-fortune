<?php

declare(strict_types=1);

namespace App\Repository\Criteria;

use Doctrine\Common\Collections\Criteria;

final class FortuneCookiesStillInProduction
{
    public static function create(): Criteria
    {
        return Criteria::create()
            ->andWhere(Criteria::expr()->eq('discontinued', false));
    }
}

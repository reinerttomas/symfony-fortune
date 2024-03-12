<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\FortuneCookie;
use Zenstruck\Foundry\ModelFactory;

use function Zenstruck\Foundry\faker;

/**
 * @extends ModelFactory<FortuneCookie>
 */
class FortuneCookieFactory extends ModelFactory
{
    protected static function getClass(): string
    {
        return FortuneCookie::class;
    }

    protected function getDefaults(): array
    {
        return [
            'category' => CategoryFactory::new(),
            'fortune' => faker()->sentence(),
            'numberPrinted' => faker()->numberBetween(0, 100),
            'discontinued' => faker()->boolean(),
        ];
    }
}

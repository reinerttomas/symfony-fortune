<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Category;
use Zenstruck\Foundry\ModelFactory;

use function Zenstruck\Foundry\faker;

/**
 * @extends ModelFactory<Category>
 */
class CategoryFactory extends ModelFactory
{
    protected static function getClass(): string
    {
        return Category::class;
    }

    protected function getDefaults(): array
    {
        return [
            'name' => faker()->words(3, true),
            'iconKey' => 'fa-question',
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Data;

readonly class CategoryFortuneStats
{
    public function __construct(
        public int $fortunesPrinted,
        public float $fortunesAverage,
        public string $categoryName,
    ) {
    }
}

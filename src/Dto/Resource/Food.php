<?php

namespace App\Dto\Resource;

use App\Enum\Type;
use App\Enum\Unit;
use Ramsey\Uuid\UuidInterface;

class Food
{
    public function __construct(
        public UuidInterface $id,
        public string $name,
        public Type $type,
        public int $quantity,
        public Unit $unit,
    ) {
        if ($quantity < 0) {
            throw new \InvalidArgumentException('Quantity must be a non-negative integer.');
        }
    }
}
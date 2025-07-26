<?php

namespace App\Collection;

use App\Enum\Type;

class FruitCollection extends FoodCollection
{
    public function supports(object $item): bool
    {
        return parent::supports($item) && $item->type === Type::FRUIT;
    }
}
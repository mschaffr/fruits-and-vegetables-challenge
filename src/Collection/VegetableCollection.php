<?php

namespace App\Collection;

use App\Enum\Type;

class VegetableCollection extends FoodCollection
{
    public function supports(object $item): bool
    {
        return parent::supports($item) && $item->type === Type::VEGETABLE;
    }
}
<?php

namespace App\Dto\Request;

use App\Enum\Type;
use App\Enum\Unit;

class QueryFood
{
    #[Assert\Choice(choices: [Type::FRUIT->value, Type::VEGETABLE->value])]
    public ?string $type = null;

    #[Assert\Type('string')]
    public ?string $name = null;

    public ?NumberRangeFilter $qty = null;

    #[Assert\Choice(choices: [Unit::GRAMS->value, Unit::KILOGRAMS->value])]
    public ?string $unit = null;
}
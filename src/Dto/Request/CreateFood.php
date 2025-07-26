<?php

namespace App\Dto\Request;

use App\Enum\Type;
use App\Enum\Unit;
use Symfony\Component\Validator\Constraints as Assert;

class CreateFood
{
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    public ?string $name;

    #[Assert\NotBlank]
    #[Assert\Choice(choices: [Type::FRUIT->value, Type::VEGETABLE->value])]
    public ?string $type;

    #[Assert\NotNull]
    #[Assert\Type('integer')]
    public ?int $quantity;

    #[Assert\NotBlank]
    #[Assert\Choice(choices: [Unit::GRAMS->value, Unit::KILOGRAMS->value])]
    public ?string $unit;

    public function __construct(?string $name, ?string $type, ?int $quantity, ?string $unit)
    {
        $this->name = $name;
        $this->type = $type;
        $this->quantity = $quantity;
        $this->unit = $unit;
    }
}


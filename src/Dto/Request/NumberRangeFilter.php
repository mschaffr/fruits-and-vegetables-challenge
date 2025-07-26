<?php

namespace App\Dto\Request;

class NumberRangeFilter
{
    #[Assert\Type('integer')]
    public ?int $lt = null;
    #[Assert\Type('integer')]
    public ?int $lte = null;
    #[Assert\Type('integer')]
    public ?int $gte = null;
    #[Assert\Type('integer')]
    public ?int $gt = null;
    #[Assert\Type('integer')]
    public ?int $eq = null;
}
<?php

namespace App\Factory;

use App\Converter\UnitConverter;
use App\Dto\Request\CreateFood;
use App\Dto\Resource\Food;
use App\Enum\Type;
use App\Enum\Unit;
use Ramsey\Uuid\Uuid;

class FoodDtoFactory
{
    public static function fromImport(array $data): Food
    {
        [$quantity, $unit] = UnitConverter::toGrams($data['quantity'], Unit::from($data['unit']));

        return new Food(
            Uuid::uuid4(),
            $data['name'],
            Type::from($data['type']),
            $quantity,
            $unit
        );
    }

    public static function fromDatabase(array $data): Food
    {
        return new Food(
            Uuid::fromString($data['id']),
            $data['name'],
            Type::from($data['type']),
            (int)$data['quantity'],
            Unit::from($data['unit'])
        );
    }

    public static function fromHttpRequest(CreateFood $request): Food
    {
        $quantity = $request->quantity;
        $unit = Unit::from($request->unit);
        if ($unit !== Unit::GRAMS) {
            [$quantity, $unit] = UnitConverter::toGrams($quantity, $unit);
        }

        return new Food(
            Uuid::uuid4(),
            $request->name,
            Type::from($request->type),
            $quantity,
            $unit
        );
    }
}
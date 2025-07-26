<?php

namespace App\Service;

use App\Collection\Collection;
use App\Collection\CollectionResolver;
use App\Converter\UnitConverter;
use App\Database\FoodDatabase;
use App\Dto\Request\CreateFood;
use App\Dto\Request\QueryFood;
use App\Dto\Resource\Food;
use App\Enum\Type;
use App\Enum\Unit;
use App\Factory\FoodDtoFactory;

class FoodService
{
    public function __construct(
        private readonly CollectionResolver $collectionResolver,
        private readonly FoodDatabase $database,
    ) {
    }

    public function getByQuery(QueryFood $query): Collection
    {
        $collection = $this->collectionResolver->resolve(Type::tryFrom($query->type ?: ''))->filter($query);

        // The conversion can be moved into a decorator or similar manipulation pattern
        if ($query->unit !== null) {
            foreach ($collection as $food) {
                [$food->quantity, $food->unit] = UnitConverter::to(
                    $food->quantity,
                    $food->unit,
                    Unit::from($query->unit)
                );
            }
        }

        return $collection;
    }

    public function createRecord(CreateFood $createFood): Food
    {
        $collection = $this->collectionResolver->resolve(Type::tryFrom($createFood->type));
        $food = FoodDtoFactory::fromHttpRequest($createFood);
        $collection->add($food);
        $this->database->persist($collection, $food->type->value);

        return $food;
    }
}
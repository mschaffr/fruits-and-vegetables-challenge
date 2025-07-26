<?php

namespace App\Collection;

use App\Database\FoodDatabase;
use App\Enum\Type;

class CollectionResolver
{
    public function __construct(private readonly FoodDatabase $database)
    {
    }

    public function resolve(?Type $type): Collection
    {
        $collection = $this->createCollection($type);
        $foods = $this->loadFoods($type);

        foreach ($foods as $food) {
            $collection->add($food);
        }

        return $collection;
    }

    private function createCollection(?Type $type): Collection
    {
        if ($type === null) {
            return new FoodCollection();
        }

        // You can pass a value map as well, or use a factory method or symfony service locator
        // to create the collection based on the type. Then you can consider to pass database to the collections
        // to load the data directly instead of doing it here.
        // This is just a simple example in time constraint.
        return match ($type) {
            Type::FRUIT => new FruitCollection(),
            Type::VEGETABLE => new VegetableCollection(),
            default => throw new \InvalidArgumentException(sprintf('Unsupported type: %s', $type->value)),
        };
    }

    private function loadFoods(?Type $type): array
    {
        if ($type === null) {
            return [
                ...$this->database->load(Type::FRUIT->value),
                ...$this->database->load(Type::VEGETABLE->value)
            ];
        }

        return [...$this->database->load($type->value)];
    }
}
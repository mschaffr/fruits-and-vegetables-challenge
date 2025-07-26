<?php

namespace App\Tests\Unit\Service;

use App\Enum\Type;
use App\Enum\Unit;
use App\Service\FoodImporter;
use PHPUnit\Framework\TestCase;

class FoodImporterTest extends TestCase
{
    public function testKgIsConvertedToG()
    {
        $foods = [
            [
                'id' => 1,
                'name' => 'Apple',
                'type' => 'fruit',
                'quantity' => 2,
                'unit' => 'kg'
            ]
        ];

        $importer = new FoodImporter();
        $importer->import($foods);

        $collections = $importer->getCollections();
        $fruitCollection = $collections[Type::FRUIT->value];
        $fruits = $fruitCollection->list();

        $this->assertCount(1, $fruits);
        $apple = $fruits[0];
        $this->assertEquals(2000, $apple->quantity);
        $this->assertEquals(Unit::GRAMS, $apple->unit);
    }

    public function testMultipleCollectionsAreCreated()
    {
        $foods = [
            [
                'id' => 1,
                'name' => 'Apple',
                'type' => 'fruit',
                'quantity' => 1,
                'unit' => 'kg'
            ],
            [
                'id' => 2,
                'name' => 'Carrot',
                'type' => 'vegetable',
                'quantity' => 500,
                'unit' => 'g'
            ]
        ];

        $importer = new FoodImporter();
        $importer->import($foods);

        $collections = $importer->getCollections();

        $this->assertArrayHasKey(Type::FRUIT->value, $collections);
        $this->assertArrayHasKey(Type::VEGETABLE->value, $collections);

        $this->assertCount(1, $collections[Type::FRUIT->value]->list());
        $this->assertCount(1, $collections[Type::VEGETABLE->value]->list());
    }
}

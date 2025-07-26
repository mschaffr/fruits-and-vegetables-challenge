<?php

namespace App\Tests\Integration\Controller;

use App\Collection\FruitCollection;
use App\Collection\VegetableCollection;
use App\Database\FoodDatabase;
use App\Dto\Resource\Food;
use App\Enum\Type;
use App\Enum\Unit;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FoodsControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $db = new FoodDatabase($this->getContainer()->getParameter('kernel.project_dir'));
        $fruits = new FruitCollection();
        $fruits->add(new Food(Uuid::uuid4(), 'Apple', Type::FRUIT, 10000, Unit::GRAMS));
        $vegetables = new VegetableCollection();
        $vegetables->add(new Food(Uuid::uuid4(), 'Banana', Type::VEGETABLE, 5000, Unit::GRAMS));
        $db->persist($fruits, Type::FRUIT->value);
        $db->persist($vegetables, Type::VEGETABLE->value);
    }

    public function testGetFoodsReturnsList(): void
    {
        $this->client->request('GET', '/foods');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json');
        $data = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertIsArray($data);
        foreach ($data['data'] as $food) {
            $this->assertArrayHasKey('id', $food);
            $this->assertArrayHasKey('name', $food);
            $this->assertArrayHasKey('type', $food);
            $this->assertArrayHasKey('quantity', $food);
            $this->assertArrayHasKey('unit', $food);
            $this->assertContains($food['type'], ['fruit', 'vegetable']);
            $this->assertContains($food['unit'], ['kg', 'g']);
        }
    }

    public function testGetFoodsWithQueryParams(): void
    {
        $this->client->request('GET', '/foods?name=Apple&unit=kg');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json');
        $data = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertIsArray($data);
        foreach ($data['data'] as $food) {
            $this->assertArrayHasKey('name', $food);
            $this->assertEquals('Apple', $food['name']);
            $this->assertArrayHasKey('unit', $food);
            $this->assertEquals(10, $food['quantity']);
        }
    }

    public function testGetFoodsWithQueryParamsQuantityFilter(): void
    {
        $this->client->request('GET', '/foods?qty[lte]=8');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json');
        $data = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertIsArray($data);
        foreach ($data['data'] as $food) {
            $this->assertArrayHasKey('name', $food);
            $this->assertEquals('Banana', $food['name']);
        }
    }

    public function testPostFoodsCreatesFood(): void
    {
        $payload = [
            'name' => 'Carrot',
            'type' => 'vegetable',
            'quantity' => 7,
            'unit' => 'kg'
        ];
        $this->client->request(
            'POST',
            '/foods',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($payload)
        );

        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/json');
        $data = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('id', $data);
        $this->assertEquals('Carrot', $data['name']);
        $this->assertEquals('vegetable', $data['type']);
        $this->assertEquals(7000, $data['quantity']);
        $this->assertEquals('g', $data['unit']);
    }

    public function testPostFoodsWithInvalidPayloadReturnsError(): void
    {
        $payload = [
            'name' => '',
            'type' => 'fruit',
            'quantity' => null,
            'unit' => 'g'
        ];
        $this->client->request(
            'POST',
            '/foods',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($payload)
        );

        $this->assertGreaterThanOrEqual(400, $this->client->getResponse()->getStatusCode());
    }
}

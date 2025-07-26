<?php

namespace App\Tests\Unit\Command;

use App\Command\ImportFoodsCommand;
use App\Database\FoodDatabase;
use App\Service\FoodImporter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class ImportFoodsCommandTest extends TestCase
{
    private string $testFile;
    private ImportFoodsCommand $command;

    protected function setUp(): void
    {
        $this->testFile = sys_get_temp_dir() . '/test_foods.json';
        $this->command = new ImportFoodsCommand(new FoodImporter(), new FoodDatabase(sys_get_temp_dir()));
    }

    protected function tearDown(): void
    {
        if (file_exists($this->testFile)) {
            unlink($this->testFile);
        }
    }

    public function testExecuteSuccess(): void
    {
        $data = [
            [
                'id' => 1,
                'name' => 'Apple',
                'type' => 'fruit',
                'quantity' => 10,
                'unit' => 'kg'
            ],
            [
                'id' => 2,
                'name' => 'Carrot',
                'type' => 'vegetable',
                'quantity' => 5,
                'unit' => 'kg'
            ]
        ];
        file_put_contents($this->testFile, json_encode($data));

        $input = new ArrayInput(['file' => $this->testFile]);
        $output = new BufferedOutput();

        $result = $this->command->run($input, $output);

        $this->assertEquals(Command::SUCCESS, $result);

        $content = $output->fetch();
        $this->assertStringContainsString('Added food item: Apple (fruit)', $content);
        $this->assertStringContainsString('Added food item: Carrot (vegetable)', $content);
        $this->assertStringContainsString('Food data imported successfully', $content);
    }

    public function testFileNotFound(): void
    {
        $input = new ArrayInput(['file' => '/nonexistent/file.json']);
        $output = new BufferedOutput();

        $result = $this->command->run($input, $output);

        $this->assertEquals(Command::FAILURE, $result);
        $this->assertStringContainsString('File not found', $output->fetch());
    }

    public function testInvalidJson(): void
    {
        file_put_contents($this->testFile, '{invalid json}');
        $input = new ArrayInput(['file' => $this->testFile]);
        $output = new BufferedOutput();

        $result = $this->command->run($input, $output);

        $this->assertEquals(Command::FAILURE, $result);
        $this->assertStringContainsString('Invalid JSON format', $output->fetch());
    }

    public function testMissingFields(): void
    {
        $data = [
            [
                'id' => 1,
                'name' => 'Apple',
            ]
        ];
        file_put_contents($this->testFile, json_encode($data));

        $input = new ArrayInput(['file' => $this->testFile]);
        $output = new BufferedOutput();

        $result = $this->command->run($input, $output);

        $this->assertEquals(Command::FAILURE, $result);
        $this->assertStringContainsString('Missing required fields', $output->fetch());
    }

    public function testUnitIsConvertedFromKgToG(): void
    {
        $data = [
            [
                'id' => 1,
                'name' => 'Banana',
                'type' => 'fruit',
                'quantity' => 2,
                'unit' => 'kg'
            ]
        ];
        file_put_contents($this->testFile, json_encode($data));

        $importer = new FoodImporter();
        $importer->import($data);

        $collections = $importer->getCollections();
        $fruitCollection = $collections['fruit'];
        $fruits = $fruitCollection->list();

        $this->assertCount(1, $fruits);
        $banana = $fruits[0];
        $this->assertEquals(2000, $banana->quantity);
        $this->assertEquals('g', $banana->unit->value);
    }
}
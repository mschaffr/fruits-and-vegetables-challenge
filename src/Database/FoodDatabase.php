<?php

namespace App\Database;

use App\Collection\Collection;
use App\Dto\Resource\Food;
use App\Factory\FoodDtoFactory;
use JsonException;

/**
 * This is just a dummy implementation to provide the basic data access.
 * It's not made pretty or optimized and should just do its job to load and deload.
 */
class FoodDatabase
{
    private string $fileDir;
    const string FILE_PATH = '/var/data/';
    const string FILE_EXTENSION = '.json';

    public function __construct(string $projectDir)
    {
        $this->fileDir = $projectDir . self::FILE_PATH;
    }

    public function persist(Collection $collection, string $type): void
    {
        $json = json_encode(
            array_map(fn($food) => get_object_vars($food), $collection->list()),
            JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR
        );

        if (!is_dir($this->fileDir)) {
            mkdir($this->fileDir, 0755, true);
        }

        file_put_contents($this->fileDir . $type . self::FILE_EXTENSION, $json);
    }

    /**
     * @return array<Food>
     */
    public function load(string $type): array
    {
        $filePath = $this->fileDir . $type . self::FILE_EXTENSION;
        if (!file_exists($filePath)) {
            return [];
        }

        $data = file_get_contents($filePath);
        if ($data === false) {
            return [];
        }

        try {
            $foods = json_decode(json: $data, associative: true, flags: JSON_THROW_ON_ERROR);
            return array_map(fn($item) => FoodDtoFactory::fromDatabase($item), $foods);
        } catch (JsonException $e) {
            return [];
        }
    }
}


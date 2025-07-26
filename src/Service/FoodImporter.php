<?php

namespace App\Service;

use App\Collection\FruitCollection;
use App\Collection\VegetableCollection;
use App\Enum\Type;
use App\Factory\FoodDtoFactory;
use Exception;
use ValueError;

class FoodImporter
{
    private array $collections;
    private array $infos = [];
    private array $errors = [];

    public function __construct()
    {
        $this->collections = [
            Type::FRUIT->value => new FruitCollection(),
            Type::VEGETABLE->value => new VegetableCollection(),
        ];
    }

    public function import(array $foods): void
    {
        foreach ($foods as $food) {
            if (!isset($food['name'], $food['type'], $food['quantity'], $food['unit'])) {
                $this->errors[] = 'Missing required fields in food data.';

                return;
            }

            try {
                $foodObj = FoodDtoFactory::fromImport($food);

                if (!isset($this->collections[$foodObj->type->value])) {
                    $this->errors[] = sprintf('Unsupported food type: %s', $foodObj->type->value);
                    continue;
                }
            } catch (ValueError $e) {
                $this->errors[] = sprintf(
                    'Invalid food type or unit for item "%s": %s',
                    $food['name'],
                    $e->getMessage()
                );
                continue;
            } catch (Exception $e) {
                $this->errors[] = sprintf('Error creating food item "%s": %s', $food['name'], $e->getMessage());
                continue;
            }

            try {
                $this->collections[$foodObj->type->value]->add($foodObj);
            } catch (Exception $e) {
                $this->errors[] = sprintf('Error adding food item "%s": %s', $foodObj->name, $e->getMessage());
                continue;
            }

            $this->infos[] = sprintf('Added food item: %s (%s)', $foodObj->name, $foodObj->type->value);
        }
    }

    public function getCollections(): array
    {
        return $this->collections;
    }

    public function getInfoMessages(): array
    {
        return $this->infos;
    }

    public function getErrorMessages(): array
    {
        return $this->errors;
    }

    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }
}

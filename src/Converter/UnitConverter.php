<?php

namespace App\Converter;

use App\Enum\Unit;

class UnitConverter
{
    public static function toGrams(float|int $quantity, Unit $unit): array
    {
        switch ($unit) {
            case Unit::GRAMS:
                return [$quantity, Unit::GRAMS];
            case Unit::KILOGRAMS:
                return [$quantity * 1000, Unit::GRAMS];
            default:
                // If unknown or not convertible, return as is
                // If new units could not be converted, throwing errors and error handling is required
                return [$quantity, $unit];
        }
    }

    public static function toKilograms(float|int $quantity, Unit $unit): array
    {
        switch ($unit) {
            case Unit::GRAMS:
                return [$quantity / 1000, Unit::KILOGRAMS];
            case Unit::KILOGRAMS:
                return [$quantity, Unit::KILOGRAMS];
            default:
                // If unknown or not convertible, return as is
                // If new units could not be converted, throwing errors and error handling is required
                return [$quantity, $unit];
        }
    }

    public static function to(float|int $quantity, Unit $fromUnit, Unit $toUnit): array
    {
        // Use $fromUnit to determine if the conversion is allowed to do, e.g. from liters to grams wouldn't be valid
        // a conversion matrix would need to be created for more complex conversions

        if ($fromUnit === Unit::GRAMS && $toUnit === Unit::GRAMS) {
            return [$quantity, $toUnit];
        }

        return match ($toUnit) {
            Unit::KILOGRAMS => self::toKilograms($quantity, $fromUnit),
            default => throw new \InvalidArgumentException(
                sprintf('Unit "%s" is not supported for conversion.', $toUnit->value)
            ),
        };
    }
}


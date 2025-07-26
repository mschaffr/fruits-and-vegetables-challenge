<?php

namespace App\Collection;

use App\Dto\Request\QueryFood;
use App\Dto\Resource\Food;
use App\Enum\Type;

class FoodCollection extends Collection
{
    public function supports(object $item): bool
    {
        return $item instanceof Food;
    }

    public function filter(QueryFood $queryFood): Collection
    {
        $filtered = new self();

        /** @var Food $item */
        foreach ($this->items as $item) {
            if ($queryFood->type && $item->type !== Type::from($queryFood->type)) {
                continue;
            }
            if ($queryFood->name && stripos($item->name, $queryFood->name) === false) {
                continue;
            }
            if ($queryFood->qty?->eq && $queryFood->qty->eq !== $item->quantity) {
                continue;
            }
            if ($queryFood->qty?->lt !== null && $queryFood->qty->lt <= $item->quantity) {
                continue;
            }
            if ($queryFood->qty?->lte !== null && $queryFood->qty->lte < $item->quantity) {
                continue;
            }
            if ($queryFood->qty?->gte !== null && $queryFood->qty->gte > $item->quantity) {
                continue;
            }
            if ($queryFood->qty?->gt !== null && $queryFood->qty->gt >= $item->quantity) {
                continue;
            }

            $filtered->add($item);
        }

        return $filtered;
    }
}
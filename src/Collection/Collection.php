<?php

namespace App\Collection;

use App\Dto\Request\QueryFood;
use App\Exception\UnsupportedCollectionException;
use IteratorAggregate;
use ArrayIterator;

abstract class Collection implements IteratorAggregate
{
    protected array $items = [];

    abstract public function supports(object $item): bool;

    abstract public function filter(QueryFood $queryFood): Collection;

    public function add(object $item): void
    {
        if ($this->supports($item) === false) {
            throw UnsupportedCollectionException::fromObject($item, $this);
        }

        $this->items[] = $item;
    }

    public function remove(object $item): void
    {
        if ($this->supports($item) === false) {
            throw UnsupportedCollectionException::fromObject($item, $this);
        }

        $key = array_search($item, $this->items, true);
        if ($key !== false) {
            unset($this->items[$key]);
            $this->items = array_values($this->items);
        }
    }

    public function list(): array
    {
        return $this->items;
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->items);
    }
}
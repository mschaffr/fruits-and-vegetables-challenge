<?php

namespace App\Exception;

use InvalidArgumentException;

class UnsupportedCollectionException extends InvalidArgumentException
{
    public static function fromObject(object $object, object $collection): self
    {
        return new self(sprintf(
            'Item of type %s is not supported by collection %s.',
            get_class($object),
            get_class($collection)
        ));
    }
}
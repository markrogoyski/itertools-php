<?php

declare(strict_types=1);

namespace IterTools\Util;

/**
 * @internal
 */
class IteratorFactory
{
    /**
     * @internal
     * @param iterable<mixed> $iterable
     *
     * @return \Iterator<mixed>|\IteratorIterator<mixed>|\ArrayIterator<mixed>
     */
    public static function makeIterator(iterable $iterable): \Iterator
    {
        switch (true) {
            case $iterable instanceof \Iterator:
                return $iterable;

            case $iterable instanceof \Traversable:
                return new \IteratorIterator($iterable);

            case \is_array($iterable):
                return new \ArrayIterator($iterable);
        }

        throw new \LogicException(\gettype($iterable) . ' type is not an expected iterable type (Iterator|Traversable|array)');
    }
}

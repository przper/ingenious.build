<?php

namespace Modules\Shared\Domain;

use IteratorAggregate;

/**
 * @template TValue
 * @implements IteratorAggregate<int, TValue>
 */
abstract class Collection implements IteratorAggregate
{
    /**
     * @return array<TValue>
     */
    abstract protected function getItems(): array;

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->getItems());
    }
}

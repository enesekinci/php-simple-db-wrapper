<?php

namespace EnesEkinci\PhpSimpleDBWrapper\Collect;

use ArrayIterator;
use EnesEkinci\PhpSimpleDBWrapper\Collect\Iterator;
use IteratorAggregate;

class Collect implements IteratorAggregate, Iterator
{
    private $position = 0;
    private $array = array();

    public function __construct(array $array)
    {
        $this->array = $array;
        $this->position = 0;
    }

    public function getIterator()
    {
        return new ArrayIterator(array());
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function current(): mixed
    {
        return $this->array[$this->position];
    }

    public function key(): mixed
    {
        return $this->position;
    }

    public function next(): void
    {
        ++$this->position;
    }

    public function valid(): bool
    {
        return isset($this->array[$this->position]);
    }

    public function each(callable $callback)
    {
        return array_map($callback, $this->array);
    }

    public function value(array $columns = [])
    {
        return $this->each(function ($val) use ($columns) {
            $data = [];
            foreach ($columns as $column) {
                $data[$column] = $val->{$column} ?? null;
            }
            return $data;
        });
    }
}

<?php

namespace EnesEkinci\PhpSimpleDBWrapper\Collect;

use Traversable;

interface Iterator extends Traversable
{
    /* Yöntemler */
    public function current(): mixed;
    public function key(): mixed;
    public function next(): void;
    public function rewind(): void;
    public function valid(): bool;
}

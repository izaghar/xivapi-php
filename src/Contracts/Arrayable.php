<?php

declare(strict_types=1);

namespace XivApi\Contracts;

/**
 * Interface for objects that can be converted to an array.
 *
 * @template TValue
 */
interface Arrayable
{
    /**
     * Convert the object to an array.
     *
     * @return array<string, TValue>
     */
    public function toArray(): array;
}

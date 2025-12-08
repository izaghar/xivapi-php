<?php

declare(strict_types=1);

namespace XivApi\Query\Builder;

use XivApi\Contracts\ClauseCollector;

/**
 * Builder state after calling any() on a field.
 *
 * After accessing array elements with [], you must access a sub-field with dot().
 */
readonly class ArrayOnBuilder
{
    public function __construct(
        private string $prefix,
        private string $path,
        private ClauseCollector $collector,
    ) {}

    /**
     * Access a field within the array elements.
     *
     * Example: BaseParam[].Name - access Name on each BaseParam element.
     */
    public function on(string $field): OnBuilder
    {
        return new OnBuilder(
            $this->prefix,
            $this->path.'.'.$field,
            $this->collector,
        );
    }
}

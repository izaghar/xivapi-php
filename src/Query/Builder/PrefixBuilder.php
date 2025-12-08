<?php

declare(strict_types=1);

namespace XivApi\Query\Builder;

use XivApi\Contracts\ClauseCollector;

/**
 * Builder state after calling must() or mustNot().
 *
 * Provides on() to start a field path and group() for nested groups.
 */
readonly class PrefixBuilder
{
    public function __construct(
        private string $prefix,
        private ClauseCollector $collector,
    ) {}

    /**
     * Start building a field condition.
     *
     * Example: ->must()->on('Name')->equals('Potion')
     */
    public function on(string $field): OnBuilder
    {
        return new OnBuilder(
            $this->prefix,
            $field,
            $this->collector,
        );
    }

    /**
     * Start building a condition on array elements.
     *
     * Example: ->must()->any('BaseParam')->on('Name')->equals('Spell Speed')
     */
    public function any(string $field): ArrayOnBuilder
    {
        return new ArrayOnBuilder(
            $this->prefix,
            $field.'[]',
            $this->collector,
        );
    }

    /**
     * Create a nested group with the current prefix.
     *
     * Example: ->must()->group(fn($q) => $q->on('A')->equals(1)->on('B')->equals(2))
     */
    public function group(callable $callback): ClauseCollector
    {
        $group = new GroupBuilder;
        $callback($group);

        return $this->collector->addClause($this->prefix.'('.$group->build().')');
    }
}

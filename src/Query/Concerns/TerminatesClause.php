<?php

declare(strict_types=1);

namespace XivApi\Query\Concerns;

use XivApi\Query\Builder\ArrayGroupBuilder;
use XivApi\Query\Builder\GroupBuilder;
use XivApi\Query\SearchQuery;

/**
 * Trait for terminating a where clause with comparison operators.
 *
 * Used by WhereBuilder and LocalizedWhereBuilder.
 */
trait TerminatesClause
{
    abstract private function terminate(string $operator, string|int|float|bool $value): SearchQuery|GroupBuilder|ArrayGroupBuilder;

    /**
     * Field equals value.
     */
    public function equals(string|int|float|bool $value): SearchQuery|GroupBuilder|ArrayGroupBuilder
    {
        return $this->terminate('=', $value);
    }

    /**
     * Field contains string (fuzzy match).
     */
    public function contains(string $value): SearchQuery|GroupBuilder|ArrayGroupBuilder
    {
        return $this->terminate('~', $value);
    }

    /**
     * Field is greater than value.
     */
    public function greaterThan(int|float $value): SearchQuery|GroupBuilder|ArrayGroupBuilder
    {
        return $this->terminate('>', $value);
    }

    /**
     * Field is less than value.
     */
    public function lessThan(int|float $value): SearchQuery|GroupBuilder|ArrayGroupBuilder
    {
        return $this->terminate('<', $value);
    }

    /**
     * Field is greater than or equal to value.
     */
    public function greaterOrEqual(int|float $value): SearchQuery|GroupBuilder|ArrayGroupBuilder
    {
        return $this->terminate('>=', $value);
    }

    /**
     * Field is less than or equal to value.
     */
    public function lessOrEqual(int|float $value): SearchQuery|GroupBuilder|ArrayGroupBuilder
    {
        return $this->terminate('<=', $value);
    }
}

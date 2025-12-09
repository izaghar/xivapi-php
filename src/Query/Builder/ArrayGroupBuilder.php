<?php

declare(strict_types=1);

namespace XivApi\Query\Builder;

use XivApi\Contracts\ClauseCollector;

/**
 * Builder for array field conditions (whereHas).
 *
 * Collects clauses and prepends the array path to each.
 */
class ArrayGroupBuilder implements ClauseCollector
{
    /** @var list<string> */
    private array $clauses = [];

    public function __construct(
        private readonly string $arrayPath,
        private readonly string $prefix = '',
    ) {}

    /**
     * Start building a condition using the parent's prefix.
     */
    public function where(string $field): WhereBuilder
    {
        return new WhereBuilder($this->prefix, $this->arrayPath.'.'.$field, $this);
    }

    /**
     * Start building a must not (-) condition (overrides parent prefix).
     */
    public function whereNot(string $field): WhereBuilder
    {
        return new WhereBuilder('-', $this->arrayPath.'.'.$field, $this);
    }

    /**
     * Start building an optional condition (overrides parent prefix) - acts as OR.
     */
    public function orWhere(string $field): WhereBuilder
    {
        return new WhereBuilder('', $this->arrayPath.'.'.$field, $this);
    }

    /**
     * Add a raw clause string.
     */
    public function addClause(string $clause): self
    {
        $this->clauses[] = $clause;

        return $this;
    }

    /**
     * Get all collected clauses.
     *
     * @return list<string>
     */
    public function getClauses(): array
    {
        return $this->clauses;
    }
}

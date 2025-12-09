<?php

declare(strict_types=1);

namespace XivApi\Query\Builder;

use XivApi\Contracts\ClauseCollector;
use XivApi\Query\Concerns\HandlesConditionShortcuts;

/**
 * Builder for array field conditions (whereHas).
 *
 * Collects clauses and prepends the array path to each.
 */
class ArrayGroupBuilder implements ClauseCollector
{
    use HandlesConditionShortcuts;

    /** @var list<string> */
    private array $clauses = [];

    public function __construct(
        private readonly string $arrayPath,
        private readonly string $prefix = '',
    ) {}

    /**
     * Add a must (+) condition using the parent's prefix.
     */
    public function where(string $field, string|int|float|bool|null $operatorOrValue = null, string|int|float|bool|null $value = null): self|WhereBuilder
    {
        return $this->buildCondition($this->prefix, $this->arrayPath.'.'.$field, $operatorOrValue, $value);
    }

    /**
     * Add a must not (-) condition (overrides parent prefix).
     */
    public function whereNot(string $field, string|int|float|bool|null $operatorOrValue = null, string|int|float|bool|null $value = null): self|WhereBuilder
    {
        return $this->buildCondition('-', $this->arrayPath.'.'.$field, $operatorOrValue, $value);
    }

    /**
     * Add an optional condition (overrides parent prefix) - acts as OR.
     */
    public function orWhere(string $field, string|int|float|bool|null $operatorOrValue = null, string|int|float|bool|null $value = null): self|WhereBuilder
    {
        return $this->buildCondition('', $this->arrayPath.'.'.$field, $operatorOrValue, $value);
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

<?php

declare(strict_types=1);

namespace XivApi\Query\Concerns;

use XivApi\Contracts\ClauseCollector;
use XivApi\Query\Builder\ArrayGroupBuilder;
use XivApi\Query\Builder\GroupBuilder;
use XivApi\Query\Builder\WhereBuilder;

/**
 * Trait for building search query conditions.
 *
 * Used by SearchQueryBuilder and GroupBuilder to avoid code duplication.
 */
trait BuildsConditions
{
    use HandlesConditionShortcuts;

    /** @var list<string> */
    private array $clauses = [];

    abstract public function addClause(string $clause): ClauseCollector;

    /**
     * Add a must (+) condition - results MUST match.
     */
    public function where(string $field, string|int|float|bool|null $operatorOrValue = null, string|int|float|bool|null $value = null): self|WhereBuilder
    {
        return $this->buildCondition('+', $field, $operatorOrValue, $value);
    }

    /**
     * Add a must not (-) condition - results must NOT match.
     */
    public function whereNot(string $field, string|int|float|bool|null $operatorOrValue = null, string|int|float|bool|null $value = null): self|WhereBuilder
    {
        return $this->buildCondition('-', $field, $operatorOrValue, $value);
    }

    /**
     * Add an optional condition (no prefix) - acts as OR.
     *
     * Without any + conditions, this acts as OR (matches any).
     * With + conditions, matching results get higher relevance scores.
     */
    public function orWhere(string $field, string|int|float|bool|null $operatorOrValue = null, string|int|float|bool|null $value = null): self|WhereBuilder
    {
        return $this->buildCondition('', $field, $operatorOrValue, $value);
    }

    /**
     * Add a must (+) group.
     */
    public function whereGroup(callable $callback): self
    {
        return $this->group('+', $callback);
    }

    /**
     * Add a must not (-) group.
     */
    public function whereNotGroup(callable $callback): self
    {
        return $this->group('-', $callback);
    }

    /**
     * Add an optional group (no prefix) - acts as OR.
     */
    public function orWhereGroup(callable $callback): self
    {
        return $this->group('', $callback);
    }

    /**
     * Add a must condition on array elements.
     */
    public function whereHas(string $array, callable $callback): self
    {
        return $this->arrayCondition('+', $array, $callback);
    }

    /**
     * Add a must not condition on array elements.
     */
    public function whereHasNot(string $array, callable $callback): self
    {
        return $this->arrayCondition('-', $array, $callback);
    }

    /**
     * Add an optional condition on array elements (no prefix) - acts as OR.
     */
    public function orWhereHas(string $array, callable $callback): self
    {
        return $this->arrayCondition('', $array, $callback);
    }

    /**
     * Build a grouped condition.
     */
    private function group(string $prefix, callable $callback): self
    {
        $group = new GroupBuilder;
        $callback($group);
        $this->clauses[] = $prefix.'('.$group->build().')';

        return $this;
    }

    /**
     * Build a condition on array elements.
     */
    private function arrayCondition(string $prefix, string $array, callable $callback): self
    {
        $group = new ArrayGroupBuilder($array.'[]', $prefix);
        $callback($group);
        foreach ($group->getClauses() as $clause) {
            $this->clauses[] = $clause;
        }

        return $this;
    }
}

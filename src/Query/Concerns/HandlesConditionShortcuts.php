<?php

declare(strict_types=1);

namespace XivApi\Query\Concerns;

use InvalidArgumentException;
use XivApi\Query\Builder\WhereBuilder;

/**
 * Trait for handling condition shortcuts (where('Field', value) syntax).
 *
 * Used by BuildsConditions and ArrayGroupBuilder.
 */
trait HandlesConditionShortcuts
{
    /**
     * Build a condition with optional operator shortcut.
     *
     * Supports three call signatures:
     * - buildCondition('+', 'Field') → returns WhereBuilder for chaining
     * - buildCondition('+', 'Field', 'value') → shortcut for equals
     * - buildCondition('+', 'Field', '>=', 90) → shortcut for operator
     */
    private function buildCondition(string $prefix, string $field, string|int|float|bool|null $operatorOrValue, string|int|float|bool|null $value): self|WhereBuilder
    {
        $builder = new WhereBuilder($prefix, $field, $this);

        // No additional args - return builder for chaining
        if ($operatorOrValue === null) {
            return $builder;
        }

        // Two args: where('field', value) - shortcut for equals
        if ($value === null) {
            return $builder->equals($operatorOrValue);
        }

        // Three args: where('field', operator, value)
        $operator = (string) $operatorOrValue;

        return match ($operator) {
            '=' => $builder->equals($value),
            '~' => $builder->contains((string) $value),
            '>' => $builder->greaterThan($value),
            '<' => $builder->lessThan($value),
            '>=' => $builder->greaterOrEqual($value),
            '<=' => $builder->lessOrEqual($value),
            default => throw new InvalidArgumentException("Unknown operator: $operator"),
        };
    }
}

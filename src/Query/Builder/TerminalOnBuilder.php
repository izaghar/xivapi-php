<?php

declare(strict_types=1);

namespace XivApi\Query\Builder;

use XivApi\Contracts\ClauseCollector;

/**
 * Builder state after applying language filter.
 *
 * Only terminators (equals, contains, etc.) are available.
 * No more path modifications allowed.
 */
readonly class TerminalOnBuilder
{
    public function __construct(
        protected string $prefix,
        protected string $path,
        protected ClauseCollector $collector,
    ) {}

    /**
     * Field equals value.
     */
    public function equals(string|int|float|bool $value): ClauseCollector
    {
        return $this->terminate('=', $value);
    }

    /**
     * Field contains string (fuzzy match).
     */
    public function contains(string $value): ClauseCollector
    {
        return $this->terminate('~', $value);
    }

    /**
     * Field is greater than value.
     */
    public function greaterThan(int|float $value): ClauseCollector
    {
        return $this->terminate('>', $value);
    }

    /**
     * Field is less than value.
     */
    public function lessThan(int|float $value): ClauseCollector
    {
        return $this->terminate('<', $value);
    }

    /**
     * Field is greater than or equal to value.
     */
    public function greaterOrEqual(int|float $value): ClauseCollector
    {
        return $this->terminate('>=', $value);
    }

    /**
     * Field is less than or equal to value.
     */
    public function lessOrEqual(int|float $value): ClauseCollector
    {
        return $this->terminate('<=', $value);
    }

    private function terminate(string $operator, string|int|float|bool $value): ClauseCollector
    {
        $formatted = $this->formatValue($value);

        return $this->collector->addClause($this->prefix.$this->path.$operator.$formatted);
    }

    private function formatValue(string|int|float|bool $value): string
    {
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (is_string($value)) {
            return '"'.$value.'"';
        }

        return (string) $value;
    }
}

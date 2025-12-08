<?php

declare(strict_types=1);

namespace XivApi\Query;

use Stringable;
use XivApi\Contracts\ClauseCollector;
use XivApi\Query\Builder\ArrayOnBuilder;
use XivApi\Query\Builder\GroupBuilder;
use XivApi\Query\Builder\OnBuilder;
use XivApi\Query\Builder\PrefixBuilder;

/**
 * Fluent builder for search query expressions.
 *
 * Example: SearchQuery::on('Name')->equals('Potion')
 *          SearchQuery::must()->on('Level')->greaterOrEqual(90)
 *
 * @see https://v2.xivapi.com/docs#search
 */
class SearchQuery implements ClauseCollector, Stringable
{
    /** @var list<string> */
    private array $clauses = [];

    /**
     * Start building a field condition.
     *
     * Example: SearchQuery::on('Name')->equals('Potion')
     */
    public static function on(string $field): OnBuilder
    {
        return new OnBuilder('', $field, new self);
    }

    /**
     * Start building a condition on array elements.
     *
     * Example: SearchQuery::any('BaseParam')->on('Name')->equals('Spell Speed')
     */
    public static function any(string $field): ArrayOnBuilder
    {
        return new ArrayOnBuilder('', $field.'[]', new self);
    }

    /**
     * Start a must (+) condition.
     *
     * Example: SearchQuery::must()->on('Name')->equals('Potion')
     */
    public static function must(): PrefixBuilder
    {
        return new PrefixBuilder('+', new self);
    }

    /**
     * Start a must not (-) condition.
     *
     * Example: SearchQuery::mustNot()->on('Name')->equals('Potion')
     */
    public static function mustNot(): PrefixBuilder
    {
        return new PrefixBuilder('-', new self);
    }

    /**
     * Create a grouped condition.
     *
     * Example: SearchQuery::group(fn($q) => $q->on('A')->equals(1)->on('B')->equals(2))
     */
    public static function group(callable $callback): self
    {
        $query = new self;
        $group = new GroupBuilder;
        $callback($group);
        $query->clauses[] = '('.$group->build().')';

        return $query;
    }

    /**
     * Continue with another field condition.
     */
    public function andOn(string $field): OnBuilder
    {
        return new OnBuilder('', $field, $this);
    }

    /**
     * Continue with a must (+) condition.
     */
    public function andMust(): PrefixBuilder
    {
        return new PrefixBuilder('+', $this);
    }

    /**
     * Continue with a must not (-) condition.
     */
    public function andMustNot(): PrefixBuilder
    {
        return new PrefixBuilder('-', $this);
    }

    /**
     * Add a grouped condition.
     */
    public function andGroup(callable $callback): self
    {
        $group = new GroupBuilder;
        $callback($group);
        $this->clauses[] = '('.$group->build().')';

        return $this;
    }

    /**
     * Add a must group.
     */
    public function andMustGroup(callable $callback): self
    {
        $group = new GroupBuilder;
        $callback($group);
        $this->clauses[] = '+('.$group->build().')';

        return $this;
    }

    /**
     * Add a must not group.
     */
    public function andMustNotGroup(callable $callback): self
    {
        $group = new GroupBuilder;
        $callback($group);
        $this->clauses[] = '-('.$group->build().')';

        return $this;
    }

    /**
     * Add a raw clause string (used by fluent builders).
     */
    public function addClause(string $clause): self
    {
        $this->clauses[] = $clause;

        return $this;
    }

    public function __toString(): string
    {
        return implode(' ', $this->clauses);
    }
}

<?php

declare(strict_types=1);

namespace XivApi\Query\Builder;

use XivApi\Contracts\ClauseCollector;

/**
 * Builder for grouped query clauses.
 *
 * Collects clauses and builds them into a parenthesized group.
 */
class GroupBuilder implements ClauseCollector
{
    /** @var list<string> */
    private array $clauses = [];

    /**
     * Start building a field condition.
     */
    public function on(string $field): OnBuilder
    {
        return new OnBuilder('', $field, $this);
    }

    /**
     * Continue with another field condition.
     *
     * Alias for on() to match SearchQuery API.
     */
    public function andOn(string $field): OnBuilder
    {
        return $this->on($field);
    }

    /**
     * Start building a condition on array elements.
     */
    public function any(string $field): ArrayOnBuilder
    {
        return new ArrayOnBuilder('', $field.'[]', $this);
    }

    /**
     * Start a must (+) condition.
     */
    public function must(): PrefixBuilder
    {
        return new PrefixBuilder('+', $this);
    }

    /**
     * Continue with a must (+) condition.
     *
     * Alias for must() to match SearchQuery API.
     */
    public function andMust(): PrefixBuilder
    {
        return $this->must();
    }

    /**
     * Start a must not (-) condition.
     */
    public function mustNot(): PrefixBuilder
    {
        return new PrefixBuilder('-', $this);
    }

    /**
     * Continue with a must not (-) condition.
     *
     * Alias for mustNot() to match SearchQuery API.
     */
    public function andMustNot(): PrefixBuilder
    {
        return $this->mustNot();
    }

    /**
     * Create a nested group.
     */
    public function group(callable $callback): self
    {
        $group = new self;
        $callback($group);
        $this->clauses[] = '('.$group->build().')';

        return $this;
    }

    /**
     * Add a grouped condition.
     *
     * Alias for group() to match SearchQuery API.
     */
    public function andGroup(callable $callback): self
    {
        return $this->group($callback);
    }

    /**
     * Add a must group.
     */
    public function andMustGroup(callable $callback): self
    {
        $group = new self;
        $callback($group);
        $this->clauses[] = '+('.$group->build().')';

        return $this;
    }

    /**
     * Add a must not group.
     */
    public function andMustNotGroup(callable $callback): self
    {
        $group = new self;
        $callback($group);
        $this->clauses[] = '-('.$group->build().')';

        return $this;
    }

    public function addClause(string $clause): self
    {
        $this->clauses[] = $clause;

        return $this;
    }

    /**
     * Build the group into a query string.
     */
    public function build(): string
    {
        return implode(' ', $this->clauses);
    }
}

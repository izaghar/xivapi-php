<?php

declare(strict_types=1);

namespace XivApi\Query\Builder;

use XivApi\Contracts\ClauseCollector;
use XivApi\Query\Concerns\BuildsConditions;

/**
 * Builder for grouped query clauses.
 *
 * Collects clauses and builds them into a parenthesized group.
 * The prefix (+, -, or none) determines how the group is treated,
 * while the content inside determines AND/OR behavior.
 */
class GroupBuilder implements ClauseCollector
{
    use BuildsConditions;

    /**
     * Add a raw clause string.
     */
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

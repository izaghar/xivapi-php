<?php

declare(strict_types=1);

namespace XivApi\Query\Builder;

use Stringable;
use XivApi\Contracts\ClauseCollector;
use XivApi\Query\Concerns\BuildsConditions;

/**
 * Fluent builder for search query expressions.
 *
 * @see https://v2.xivapi.com/docs#search
 */
class SearchQueryBuilder implements ClauseCollector, Stringable
{
    use BuildsConditions;

    /**
     * Add a raw clause string (used by fluent builders).
     */
    public function addClause(string $clause): self
    {
        $this->clauses[] = $clause;

        return $this;
    }

    /**
     * Build the query string.
     */
    public function __toString(): string
    {
        return implode(' ', $this->clauses);
    }
}

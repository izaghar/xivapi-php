<?php

declare(strict_types=1);

namespace XivApi\Contracts;

/**
 * Interface for objects that collect query clauses.
 *
 * Implemented by SearchQuery and used by all builders.
 */
interface ClauseCollector
{
    /**
     * Add a clause to the query.
     *
     * @return $this
     */
    public function addClause(string $clause): self;
}

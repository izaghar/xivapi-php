<?php

declare(strict_types=1);

namespace XivApi\Client\Concerns;

use Stringable;

/**
 * Adds search query parameter support to a client.
 */
trait HasQuery
{
    private ?string $query = null;

    /**
     * Set the search query.
     *
     * Accepts a raw query string or a SearchQuery builder.
     */
    public function query(string|Stringable $query): self
    {
        $this->query = (string) $query;

        return $this;
    }
}

<?php

declare(strict_types=1);

namespace XivApi\Client\Concerns;

/**
 * Provides limit configuration.
 */
trait HasLimit
{
    private ?int $limit = null;

    /**
     * Set the maximum number of results to return.
     */
    public function limit(int $limit): self
    {
        $this->limit = $limit;

        return $this;
    }
}

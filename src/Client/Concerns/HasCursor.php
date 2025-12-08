<?php

declare(strict_types=1);

namespace XivApi\Client\Concerns;

/**
 * Adds cursor pagination support to a client.
 */
trait HasCursor
{
    private ?string $cursor = null;

    /**
     * Set the pagination cursor from a previous response.
     *
     * When using a cursor, query and sheets parameters are ignored.
     */
    public function cursor(string $cursor): self
    {
        $this->cursor = $cursor;

        return $this;
    }
}

<?php

declare(strict_types=1);

namespace XivApi\Client\Concerns;

/**
 * Adds version parameter support to a client.
 */
trait HasVersion
{
    private ?string $version = null;

    /**
     * Set the game version to use for this query.
     */
    public function version(string $version): self
    {
        $this->version = $version;

        return $this;
    }
}

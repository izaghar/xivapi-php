<?php

declare(strict_types=1);

namespace XivApi\Client\Concerns;

/**
 * Provides schema configuration.
 */
trait HasSchema
{
    private ?string $schema = null;

    /**
     * Set the schema to use for reading data.
     */
    public function schema(string $schema): self
    {
        $this->schema = $schema;

        return $this;
    }
}

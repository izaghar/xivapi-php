<?php

declare(strict_types=1);

namespace XivApi\Client\Concerns;

/**
 * Adds sheets parameter support to a client.
 */
trait HasSheets
{
    /** @var string[]|null */
    private ?array $sheets = null;

    /**
     * Set the sheets to search.
     *
     * @param  string[]  $sheets
     */
    public function sheets(array $sheets): self
    {
        $this->sheets = $sheets;

        return $this;
    }
}

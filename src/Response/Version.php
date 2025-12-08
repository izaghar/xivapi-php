<?php

declare(strict_types=1);

namespace XivApi\Response;

/**
 * Represents a single game version available in the API.
 */
readonly class Version
{
    /**
     * @param  string  $key  Canonical key for this version
     * @param  string[]  $names  Names associated with this version (e.g. "7.0", "latest")
     */
    public function __construct(
        public string $key,
        public array $names,
    ) {}

    /**
     * @param  array{key: string, names: string[]}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            key: $data['key'],
            names: $data['names'],
        );
    }
}

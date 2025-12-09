<?php

declare(strict_types=1);

namespace XivApi\Response;

use XivApi\Contracts\Arrayable;

/**
 * Response from the /version endpoint.
 *
 * @implements Arrayable<array<array{key: string, names: string[]}>>
 */
readonly class VersionsResponse implements Arrayable
{
    /**
     * @param  Version[]  $versions  List of available game versions
     */
    public function __construct(
        public array $versions,
    ) {}

    /**
     * @param  array{versions: array<array{key: string, names: string[]}>}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            versions: array_map(
                fn (array $version) => Version::fromArray($version),
                $data['versions'],
            ),
        );
    }

    public function toArray(): array
    {
        return [
            'versions' => array_map(
                fn (Version $version) => $version->toArray(),
                $this->versions,
            ),
        ];
    }
}

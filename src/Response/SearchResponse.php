<?php

declare(strict_types=1);

namespace XivApi\Response;

/**
 * Response from the search endpoint.
 */
readonly class SearchResponse
{
    /**
     * @param  SearchResult[]  $results  Array of search results sorted by relevance
     * @param  string  $schema  Canonical specifier for the schema used
     * @param  string  $version  Canonical specifier for the version used
     * @param  string|null  $next  Cursor for retrieving further results
     */
    public function __construct(
        public array $results,
        public string $schema,
        public string $version,
        public ?string $next = null,
    ) {}

    /**
     * @param  array{results: array<array{score: float, sheet: string, row_id: int, subrow_id?: int|null, fields: array<string, mixed>, transient?: array<string, mixed>|null}>, schema: string, version: string, next?: string|null}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            results: array_map(
                fn (array $row) => SearchResult::fromArray($row),
                $data['results'],
            ),
            schema: $data['schema'],
            version: $data['version'],
            next: $data['next'] ?? null,
        );
    }

    /**
     * Check if more results are available.
     */
    public function hasMore(): bool
    {
        return $this->next !== null;
    }
}

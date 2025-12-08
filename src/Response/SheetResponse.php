<?php

declare(strict_types=1);

namespace XivApi\Response;

/**
 * Response from GET /sheet/{sheet} - rows from a sheet.
 */
readonly class SheetResponse
{
    /**
     * @param  Row[]  $rows  Array of rows retrieved
     * @param  string  $schema  Canonical specifier for the schema used
     * @param  string  $version  Canonical specifier for the version used
     */
    public function __construct(
        public array $rows,
        public string $schema,
        public string $version,
    ) {}

    /**
     * @param  array{rows: array<array{row_id: int, subrow_id?: int|null, fields: array<string, mixed>, transient?: array<string, mixed>|null}>, schema: string, version: string}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            rows: array_map(
                fn (array $row) => Row::fromArray($row),
                $data['rows'],
            ),
            schema: $data['schema'],
            version: $data['version'],
        );
    }
}

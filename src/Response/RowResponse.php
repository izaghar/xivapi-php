<?php

declare(strict_types=1);

namespace XivApi\Response;

use XivApi\Contracts\Arrayable;

/**
 * Response from GET /sheet/{sheet}/{row} - a single row.
 *
 * @implements Arrayable<mixed>
 */
readonly class RowResponse implements Arrayable
{
    /**
     * @param  int  $rowId  ID of this row
     * @param  int|null  $subrowId  Subrow ID, when relevant
     * @param  array<string, mixed>  $fields  Field values according to the schema and field filter
     * @param  array<string, mixed>|null  $transient  Transient row fields, if present
     * @param  string  $schema  Canonical specifier for the schema used
     * @param  string  $version  Canonical specifier for the version used
     */
    public function __construct(
        public int $rowId,
        public ?int $subrowId,
        public array $fields,
        public ?array $transient,
        public string $schema,
        public string $version,
    ) {}

    /**
     * @param  array{row_id: int, subrow_id?: int|null, fields: array<string, mixed>, transient?: array<string, mixed>|null, schema: string, version: string}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            rowId: $data['row_id'],
            subrowId: $data['subrow_id'] ?? null,
            fields: $data['fields'],
            transient: $data['transient'] ?? null,
            schema: $data['schema'],
            version: $data['version'],
        );
    }

    public function toArray(): array
    {
        return [
            'row_id' => $this->rowId,
            'subrow_id' => $this->subrowId,
            'fields' => $this->fields,
            'transient' => $this->transient,
            'schema' => $this->schema,
            'version' => $this->version,
        ];
    }
}

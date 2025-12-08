<?php

declare(strict_types=1);

namespace XivApi\Response;

/**
 * Represents a single row from a sheet.
 */
readonly class Row
{
    /**
     * @param  int  $rowId  ID of this row
     * @param  int|null  $subrowId  Subrow ID, when relevant
     * @param  array<string, mixed>  $fields  Field values according to the schema and field filter
     * @param  array<string, mixed>|null  $transient  Transient row fields, if present
     */
    public function __construct(
        public int $rowId,
        public ?int $subrowId,
        public array $fields,
        public ?array $transient = null,
    ) {}

    /**
     * @param  array{row_id: int, subrow_id?: int|null, fields: array<string, mixed>, transient?: array<string, mixed>|null}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            rowId: $data['row_id'],
            subrowId: $data['subrow_id'] ?? null,
            fields: $data['fields'],
            transient: $data['transient'] ?? null,
        );
    }
}

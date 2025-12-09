<?php

declare(strict_types=1);

namespace XivApi\Response;

/**
 * Represents a single search result.
 *
 * Extends Row with search-specific fields like score and sheet name.
 */
readonly class SearchResult extends Row
{
    /**
     * @param  float  $score  Relevance score for this result
     * @param  string  $sheet  Excel sheet this result was found in
     * @param  int  $rowId  ID of this row
     * @param  int|null  $subrowId  Subrow ID, when relevant
     * @param  array<string, mixed>  $fields  Field values according to the schema and field filter
     * @param  array<string, mixed>|null  $transient  Transient row fields, if present
     */
    public function __construct(
        public float $score,
        public string $sheet,
        int $rowId,
        ?int $subrowId,
        array $fields,
        ?array $transient = null,
    ) {
        parent::__construct($rowId, $subrowId, $fields, $transient);
    }

    /**
     * @param  array{score: float, sheet: string, row_id: int, subrow_id?: int|null, fields: array<string, mixed>, transient?: array<string, mixed>|null}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            score: $data['score'],
            sheet: $data['sheet'],
            rowId: $data['row_id'],
            subrowId: $data['subrow_id'] ?? null,
            fields: $data['fields'],
            transient: $data['transient'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'score' => $this->score,
            'sheet' => $this->sheet,
            ...parent::toArray(),
        ];
    }
}

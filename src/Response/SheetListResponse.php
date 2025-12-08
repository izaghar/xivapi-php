<?php

declare(strict_types=1);

namespace XivApi\Response;

/**
 * Response from GET /sheet - list of available sheets.
 */
readonly class SheetListResponse
{
    /**
     * @param  string[]  $sheets  Names of available sheets
     */
    public function __construct(
        public array $sheets,
    ) {}

    /**
     * @param  array{sheets: array<array{name: string}>}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            sheets: array_map(
                fn (array $sheet) => $sheet['name'],
                $data['sheets'],
            ),
        );
    }
}

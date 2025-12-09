<?php

declare(strict_types=1);

namespace XivApi\Response;

use XivApi\Contracts\Arrayable;

/**
 * Response from GET /sheet - list of available sheets.
 *
 * @implements Arrayable<array<array{name: string}>>
 */
readonly class SheetListResponse implements Arrayable
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

    public function toArray(): array
    {
        return [
            'sheets' => array_map(
                fn (string $sheet) => ['name' => $sheet],
                $this->sheets,
            ),
        ];
    }
}

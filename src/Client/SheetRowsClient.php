<?php

declare(strict_types=1);

namespace XivApi\Client;

use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use XivApi\Client\Concerns\HasFields;
use XivApi\Client\Concerns\HasLanguage;
use XivApi\Client\Concerns\HasLimit;
use XivApi\Client\Concerns\HasSchema;
use XivApi\Client\Concerns\HasVersion;
use XivApi\Enums\Language;
use XivApi\Exception\XivApiException;
use XivApi\Response\SheetResponse;

/**
 * Client for GET /sheet/{sheet} endpoint.
 *
 * Fetches rows from a specific sheet.
 */
class SheetRowsClient extends AbstractClient
{
    use HasFields;
    use HasLanguage;
    use HasLimit;
    use HasSchema;
    use HasVersion;

    private ?string $after = null;

    /** @var int[]|null */
    private ?array $rows = null;

    /**
     * @param  Language[]  $localizations
     */
    public function __construct(
        ClientInterface $http,
        RequestFactoryInterface $requestFactory,
        string $baseUrl,
        private readonly string $sheetName,
        array $localizations = [],
        ?Language $language = null,
        ?string $version = null,
        ?string $schema = null,
    ) {
        parent::__construct($http, $requestFactory, $baseUrl);
        $this->localizations = $localizations;
        $this->language = $language;
        $this->version = $version;
        $this->schema = $schema;
    }

    protected function getPath(): string
    {
        return 'sheet/'.$this->sheetName;
    }

    /**
     * @return array<string, string|int>
     */
    protected function buildQueryParams(): array
    {
        return [
            'version' => $this->version,
            'language' => $this->language?->value,
            'schema' => $this->schema,
            'fields' => $this->buildFieldsString(),
            'transient' => $this->buildTransientString(),
            'limit' => $this->limit,
            'after' => $this->after,
            'rows' => $this->rows !== null ? implode(',', $this->rows) : null,
        ];
    }

    /**
     * Fetch rows after the specified row ID (pagination).
     */
    public function after(string|int $after): self
    {
        $this->after = (string) $after;

        return $this;
    }

    /**
     * Fetch specific rows by ID.
     *
     * @param  int[]  $rows
     */
    public function rows(array $rows): self
    {
        $this->rows = $rows;

        return $this;
    }

    /**
     * Get a client for fetching a single row.
     */
    public function row(int|string $rowId): SheetRowClient
    {
        return new SheetRowClient(
            $this->http,
            $this->requestFactory,
            $this->baseUrl,
            $this->sheetName,
            $rowId,
            $this->localizations,
            $this->language,
            $this->version,
            $this->schema,
        );
    }

    /**
     * Execute the query and return rows.
     *
     * @throws XivApiException|ClientExceptionInterface
     */
    public function get(): SheetResponse
    {
        return SheetResponse::fromArray($this->request());
    }
}

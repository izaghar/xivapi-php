<?php

declare(strict_types=1);

namespace XivApi\Client;

use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use XivApi\Client\Concerns\HasFields;
use XivApi\Client\Concerns\HasLanguage;
use XivApi\Client\Concerns\HasSchema;
use XivApi\Client\Concerns\HasVersion;
use XivApi\Enums\Language;
use XivApi\Exception\XivApiException;
use XivApi\Response\RowResponse;

/**
 * Client for GET /sheet/{sheet}/{row} endpoint.
 *
 * Fetches a single row from a sheet.
 */
class SheetRowClient extends AbstractClient
{
    use HasFields;
    use HasLanguage;
    use HasSchema;
    use HasVersion;

    /**
     * @param  Language[]  $localizations
     */
    public function __construct(
        ClientInterface $http,
        RequestFactoryInterface $requestFactory,
        string $baseUrl,
        private readonly string $sheetName,
        private readonly int|string $rowId,
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
        return 'sheet/'.$this->sheetName.'/'.$this->rowId;
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
        ];
    }

    /**
     * Execute the request and return the row.
     *
     * @throws XivApiException|ClientExceptionInterface
     */
    public function get(): RowResponse
    {
        return RowResponse::fromArray($this->request());
    }
}

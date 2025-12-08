<?php

declare(strict_types=1);

namespace XivApi\Client;

use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use XivApi\Client\Concerns\HasCursor;
use XivApi\Client\Concerns\HasFields;
use XivApi\Client\Concerns\HasLanguage;
use XivApi\Client\Concerns\HasLimit;
use XivApi\Client\Concerns\HasQuery;
use XivApi\Client\Concerns\HasSchema;
use XivApi\Client\Concerns\HasSheets;
use XivApi\Client\Concerns\HasVersion;
use XivApi\Enums\Language;
use XivApi\Exception\XivApiException;
use XivApi\Response\SearchResponse;

/**
 * Client for the /search endpoint.
 */
class SearchClient extends AbstractClient
{
    use HasCursor;
    use HasFields;
    use HasLanguage;
    use HasLimit;
    use HasQuery;
    use HasSchema;
    use HasSheets;
    use HasVersion;

    /**
     * @param  Language[]  $localizations
     */
    public function __construct(
        ClientInterface $http,
        RequestFactoryInterface $requestFactory,
        string $baseUrl,
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
        return 'search';
    }

    /**
     * @return array<string, string|int>
     */
    protected function buildQueryParams(): array
    {
        $params = [];

        if ($this->cursor !== null) {
            $params['cursor'] = $this->cursor;
        } else {
            $params['query'] = $this->query;
            $params['sheets'] = implode(',', $this->sheets ?? []);
        }

        return [
            ...$params,
            'language' => $this->language?->value,
            'schema' => $this->schema,
            'fields' => $this->buildFieldsString(),
            'transient' => $this->buildTransientString(),
            'limit' => $this->limit,
            'version' => $this->version,
        ];
    }

    /**
     * Execute the search query.
     *
     * @throws XivApiException|ClientExceptionInterface
     */
    public function get(): SearchResponse
    {
        if ($this->cursor === null && ($this->query === null || $this->sheets === null)) {
            throw new XivApiException('Search requires either a cursor or both query and sheets.');
        }

        return SearchResponse::fromArray($this->request());
    }
}

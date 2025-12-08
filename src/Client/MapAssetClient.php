<?php

declare(strict_types=1);

namespace XivApi\Client;

use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use XivApi\Client\Concerns\HasVersion;
use XivApi\Exception\XivApiException;

/**
 * Client for GET /asset/map/{territory}/{index} endpoint.
 *
 * Retrieves composed map images, automatically merging split map files.
 */
class MapAssetClient extends AbstractClient
{
    use HasVersion;

    public function __construct(
        ClientInterface $http,
        RequestFactoryInterface $requestFactory,
        string $baseUrl,
        private readonly string $territory,
        private readonly string $index,
        ?string $version = null,
    ) {
        parent::__construct($http, $requestFactory, $baseUrl);
        $this->version = $version;
    }

    protected function getPath(): string
    {
        return 'asset/map/'.$this->territory.'/'.$this->index;
    }

    /**
     * @return array<string, string|int>
     */
    protected function buildQueryParams(): array
    {
        return [
            'version' => $this->version,
        ];
    }

    /**
     * Fetch the map and return the raw binary content (JPEG).
     *
     * @throws XivApiException|ClientExceptionInterface
     */
    public function get(): string
    {
        return $this->requestRaw()->getBody()->getContents();
    }

    /**
     * Fetch the map and return the full PSR-7 response.
     *
     * Useful for streaming or accessing headers (ETag, Content-Type).
     *
     * @throws XivApiException|ClientExceptionInterface
     */
    public function fetch(): ResponseInterface
    {
        return $this->requestRaw();
    }
}

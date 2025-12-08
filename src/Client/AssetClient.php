<?php

declare(strict_types=1);

namespace XivApi\Client;

use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use XivApi\Client\Concerns\HasVersion;
use XivApi\Enums\AssetFormat;
use XivApi\Exception\XivApiException;

/**
 * Client for GET /asset endpoint.
 *
 * Retrieves game assets (icons, textures) in various formats.
 */
class AssetClient extends AbstractClient
{
    use HasVersion;

    public function __construct(
        ClientInterface $http,
        RequestFactoryInterface $requestFactory,
        string $baseUrl,
        private readonly string $path,
        private readonly AssetFormat $format,
        ?string $version = null,
    ) {
        parent::__construct($http, $requestFactory, $baseUrl);
        $this->version = $version;
    }

    protected function getPath(): string
    {
        return 'asset';
    }

    /**
     * @return array<string, string|int>
     */
    protected function buildQueryParams(): array
    {
        return [
            'version' => $this->version,
            'path' => $this->path,
            'format' => $this->format->value,
        ];
    }

    /**
     * Fetch the asset and return the raw binary content.
     *
     * @throws XivApiException|ClientExceptionInterface
     */
    public function get(): string
    {
        return $this->requestRaw()->getBody()->getContents();
    }

    /**
     * Fetch the asset and return the full PSR-7 response.
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

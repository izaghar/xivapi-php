<?php

declare(strict_types=1);

namespace XivApi\Client;

use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use XivApi\Client\Concerns\HasVersion;
use XivApi\Exception\XivApiException;
use XivApi\Response\SheetListResponse;

/**
 * Client for GET /sheet endpoint.
 *
 * Lists all available sheets.
 */
class SheetIndexClient extends AbstractClient
{
    use HasVersion;

    public function __construct(
        ClientInterface $http,
        RequestFactoryInterface $requestFactory,
        string $baseUrl,
        ?string $version = null,
    ) {
        parent::__construct($http, $requestFactory, $baseUrl);
        $this->version = $version;
    }

    protected function getPath(): string
    {
        return 'sheet';
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
     * List all available sheets.
     *
     * @throws XivApiException|ClientExceptionInterface
     */
    public function list(): SheetListResponse
    {
        return SheetListResponse::fromArray($this->request());
    }
}

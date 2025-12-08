<?php

declare(strict_types=1);

namespace XivApi\Client;

use Psr\Http\Client\ClientExceptionInterface;
use XivApi\Exception\XivApiException;
use XivApi\Response\VersionsResponse;

/**
 * Client for the /version endpoint.
 */
class VersionClient extends AbstractClient
{
    protected function getPath(): string
    {
        return 'version';
    }

    /**
     * @return array<string, string|int>
     */
    protected function buildQueryParams(): array
    {
        return [];
    }

    /**
     * List all available game versions.
     *
     * @throws XivApiException|ClientExceptionInterface
     */
    public function list(): VersionsResponse
    {
        return VersionsResponse::fromArray($this->request());
    }
}

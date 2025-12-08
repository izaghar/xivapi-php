<?php

declare(strict_types=1);

namespace XivApi\Client;

use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use XivApi\Exception\XivApiException;

/**
 * Base client with shared HTTP functionality.
 */
abstract class AbstractClient
{
    public function __construct(
        protected readonly ClientInterface $http,
        protected readonly RequestFactoryInterface $requestFactory,
        protected readonly string $baseUrl,
    ) {}

    /**
     * Get the endpoint path for this client.
     */
    abstract protected function getPath(): string;

    /**
     * Build query parameters for the request.
     *
     * @return array<string, string|int>
     */
    abstract protected function buildQueryParams(): array;

    /**
     * Get the URL that would be requested.
     */
    public function getUrl(): string
    {
        $url = $this->baseUrl.$this->getPath();
        $params = array_filter($this->buildQueryParams());

        if ($params !== []) {
            $url .= '?'.http_build_query($params);
        }

        return $url;
    }

    /**
     * Execute a GET request and return decoded JSON.
     *
     * @return array<string, mixed>
     *
     * @throws XivApiException|ClientExceptionInterface
     */
    protected function request(): array
    {
        $request = $this->requestFactory
            ->createRequest('GET', $this->getUrl())
            ->withHeader('Accept', 'application/json');

        $response = $this->http->sendRequest($request);
        $data = json_decode($response->getBody()->getContents(), true);

        if ($response->getStatusCode() !== 200) {
            throw XivApiException::fromResponse(
                $data['code'] ?? $response->getStatusCode(),
                $data['message'] ?? 'Unknown error',
            );
        }

        return $data;
    }

    /**
     * Execute a GET request and return the raw PSR-7 response.
     *
     * @throws XivApiException|ClientExceptionInterface
     */
    protected function requestRaw(): ResponseInterface
    {
        $request = $this->requestFactory->createRequest('GET', $this->getUrl());

        $response = $this->http->sendRequest($request);

        if ($response->getStatusCode() !== 200) {
            $data = json_decode($response->getBody()->getContents(), true);
            throw XivApiException::fromResponse(
                $data['code'] ?? $response->getStatusCode(),
                $data['message'] ?? 'Unknown error',
            );
        }

        return $response;
    }
}

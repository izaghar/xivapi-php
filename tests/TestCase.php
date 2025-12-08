<?php

declare(strict_types=1);

namespace XivApi\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use XivApi\XivApi;

/**
 * Helper to create a mocked XivApi client for testing.
 *
 * @param  array<Response>  $responses  Queue of responses to return
 * @param  array<array{request: RequestInterface}>  $history  Reference to capture request history
 */
function createMockedClient(array $responses, array &$history = []): XivApi
{
    $mock = new MockHandler($responses);
    $handlerStack = HandlerStack::create($mock);
    $handlerStack->push(Middleware::history($history));

    $http = new Client(['handler' => $handlerStack]);
    $requestFactory = new HttpFactory;

    return new XivApi($http, $requestFactory);
}

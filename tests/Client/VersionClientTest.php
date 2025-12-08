<?php

declare(strict_types=1);

use GuzzleHttp\Psr7\Response;
use XivApi\Exception\XivApiException;
use XivApi\Response\Version;
use XivApi\Response\VersionsResponse;

use function XivApi\Tests\createMockedClient;

require_once __DIR__.'/../TestCase.php';

it('fetches versions from the correct endpoint', function () {
    $history = [];
    $api = createMockedClient([
        new Response(200, [], json_encode([
            'versions' => [
                ['key' => 'abc123', 'names' => ['7.0']],
            ],
        ])),
    ], $history);

    $api->version()->list();

    expect($history)->toHaveCount(1)
        ->and($history[0]['request']->getUri()->__toString())
        ->toBe('https://v2.xivapi.com/api/version')
        ->and($history[0]['request']->getMethod())->toBe('GET')
        ->and($history[0]['request']->getHeaderLine('Accept'))->toBe('application/json');
});

it('parses version response correctly', function () {
    $api = createMockedClient([
        new Response(200, [], json_encode([
            'versions' => [
                ['key' => 'f815390159effefd', 'names' => ['7.0']],
                ['key' => '2139246928a48a9a', 'names' => ['7.01', 'latest']],
            ],
        ])),
    ]);

    $response = $api->version()->list();

    expect($response)->toBeInstanceOf(VersionsResponse::class)
        ->and($response->versions)->toHaveCount(2)
        ->and($response->versions[0])->toBeInstanceOf(Version::class)
        ->and($response->versions[0]->key)->toBe('f815390159effefd')
        ->and($response->versions[0]->names)->toBe(['7.0'])
        ->and($response->versions[1]->key)->toBe('2139246928a48a9a')
        ->and($response->versions[1]->names)->toBe(['7.01', 'latest']);

});

it('throws exception on error response', function () {
    $api = createMockedClient([
        new Response(400, [], json_encode([
            'code' => 400,
            'message' => 'invalid request: example error',
        ])),
    ]);

    $api->version()->list();
})->throws(XivApiException::class, 'invalid request: example error');

it('includes status code in exception', function () {
    $api = createMockedClient([
        new Response(500, [], json_encode([
            'code' => 500,
            'message' => 'internal server error',
        ])),
    ]);

    try {
        $api->version()->list();
        $this->fail('Expected XivApiException');
    } catch (XivApiException $e) {
        expect($e->statusCode)->toBe(500)
            ->and($e->getMessage())->toBe('internal server error');
    }
});

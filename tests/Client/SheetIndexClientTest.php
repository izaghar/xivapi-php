<?php

declare(strict_types=1);

use GuzzleHttp\Psr7\Response;
use XivApi\Response\SheetListResponse;

use function XivApi\Tests\createMockedClient;

require_once __DIR__.'/../TestCase.php';

it('fetches sheet list from correct endpoint', function () {
    $history = [];
    $api = createMockedClient([
        new Response(200, [], json_encode([
            'sheets' => [['name' => 'Action'], ['name' => 'Item']],
        ])),
    ], $history);

    $api->sheetIndex()->list();

    expect($history)->toHaveCount(1)
        ->and($history[0]['request']->getUri()->__toString())
        ->toBe('https://v2.xivapi.com/api/sheet');
});

it('parses sheet list correctly', function () {
    $api = createMockedClient([
        new Response(200, [], json_encode([
            'sheets' => [
                ['name' => 'Action'],
                ['name' => 'Item'],
                ['name' => 'Status'],
            ],
        ])),
    ]);

    $response = $api->sheetIndex()->list();

    expect($response)->toBeInstanceOf(SheetListResponse::class)
        ->and($response->sheets)->toBe(['Action', 'Item', 'Status']);
});

it('returns correct URL', function () {
    $api = createMockedClient([]);

    expect($api->sheetIndex()->getUrl())->toBe('https://v2.xivapi.com/api/sheet');
});

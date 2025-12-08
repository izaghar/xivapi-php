<?php

declare(strict_types=1);

use GuzzleHttp\Psr7\Response;
use XivApi\Response\RowResponse;

use function XivApi\Tests\createMockedClient;

require_once __DIR__.'/../TestCase.php';

it('builds correct URL for single row', function () {
    $history = [];
    $api = createMockedClient([
        new Response(200, [], json_encode([
            'row_id' => 4,
            'fields' => ['Name' => 'Wind Shard'],
            'schema' => 'exdschema@1',
            'version' => 'abc123',
        ])),
    ], $history);

    $api->sheet('Item')->row(4)->get();

    expect($history[0]['request']->getUri()->getPath())
        ->toBe('/api/sheet/Item/4');
});

it('parses row response correctly', function () {
    $api = createMockedClient([
        new Response(200, [], json_encode([
            'row_id' => 4,
            'subrow_id' => null,
            'fields' => ['Name' => 'Wind Shard', 'Description' => 'A small crystal'],
            'transient' => null,
            'schema' => 'exdschema@1',
            'version' => 'abc123',
        ])),
    ]);

    $response = $api->sheet('Item')->row(4)->get();

    expect($response)->toBeInstanceOf(RowResponse::class)
        ->and($response->rowId)->toBe(4)
        ->and($response->fields['Name'])->toBe('Wind Shard')
        ->and($response->schema)->toBe('exdschema@1');
});

it('supports subrow format', function () {
    $history = [];
    $api = createMockedClient([
        new Response(200, [], json_encode([
            'row_id' => 4,
            'subrow_id' => 1,
            'fields' => [],
            'schema' => 'exdschema@1',
            'version' => 'abc123',
        ])),
    ], $history);

    $api->sheet('Item')->row('4:1')->get();

    expect($history[0]['request']->getUri()->getPath())
        ->toBe('/api/sheet/Item/4:1');
});

it('returns correct URL', function () {
    $api = createMockedClient([]);

    expect($api->sheet('Item')->row(4)->getUrl())
        ->toBe('https://v2.xivapi.com/api/sheet/Item/4');
});

<?php

declare(strict_types=1);

use GuzzleHttp\Psr7\Response;
use XivApi\Response\Row;
use XivApi\Response\SheetResponse;

use function XivApi\Tests\createMockedClient;

require_once __DIR__.'/../TestCase.php';

it('builds correct endpoint path', function () {
    $history = [];
    $api = createMockedClient([
        new Response(200, [], json_encode([
            'rows' => [],
            'schema' => 'exdschema@1',
            'version' => 'abc123',
        ])),
    ], $history);

    $api->sheet('Item')->get();

    expect($history[0]['request']->getUri()->getPath())->toBe('/api/sheet/Item');
});

it('parses sheet response correctly', function () {
    $api = createMockedClient([
        new Response(200, [], json_encode([
            'rows' => [
                ['row_id' => 1, 'fields' => ['Name' => 'Gil']],
                ['row_id' => 2, 'fields' => ['Name' => 'Fire Shard']],
            ],
            'schema' => 'exdschema@1',
            'version' => 'abc123',
        ])),
    ]);

    $response = $api->sheet('Item')->get();

    expect($response)->toBeInstanceOf(SheetResponse::class)
        ->and($response->rows)->toHaveCount(2)
        ->and($response->rows[0])->toBeInstanceOf(Row::class)
        ->and($response->rows[0]->rowId)->toBe(1)
        ->and($response->rows[0]->fields['Name'])->toBe('Gil')
        ->and($response->schema)->toBe('exdschema@1')
        ->and($response->version)->toBe('abc123');
});

it('supports rows parameter', function () {
    $history = [];
    $api = createMockedClient([
        new Response(200, [], json_encode([
            'rows' => [],
            'schema' => 'exdschema@1',
            'version' => 'abc123',
        ])),
    ], $history);

    $api->sheet('Item')->rows([1, 2, 3])->get();

    parse_str($history[0]['request']->getUri()->getQuery(), $params);
    expect($params['rows'])->toBe('1,2,3');
});

it('supports after parameter', function () {
    $history = [];
    $api = createMockedClient([
        new Response(200, [], json_encode([
            'rows' => [],
            'schema' => 'exdschema@1',
            'version' => 'abc123',
        ])),
    ], $history);

    $api->sheet('Item')->after(100)->get();

    parse_str($history[0]['request']->getUri()->getQuery(), $params);
    expect($params['after'])->toBe('100');
});

it('returns correct URL', function () {
    $api = createMockedClient([]);

    expect($api->sheet('Item')->getUrl())->toBe('https://v2.xivapi.com/api/sheet/Item');
});

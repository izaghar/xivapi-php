<?php

declare(strict_types=1);

use GuzzleHttp\Psr7\Response;
use XivApi\Exception\XivApiException;
use XivApi\Query\SearchQuery;
use XivApi\Response\SearchResponse;
use XivApi\Response\SearchResult;

use function XivApi\Tests\createMockedClient;

require_once __DIR__.'/../TestCase.php';

describe('search()->get()', function () {
    it('builds correct endpoint path', function () {
        $history = [];
        $api = createMockedClient([
            new Response(200, [], json_encode([
                'results' => [],
                'schema' => 'exdschema@1',
                'version' => 'abc123',
            ])),
        ], $history);

        $api->search()
            ->query('Name~"Potion"')
            ->sheets(['Item'])
            ->get();

        expect($history[0]['request']->getUri()->getPath())->toBe('/api/search');
    });

    it('includes query parameter', function () {
        $history = [];
        $api = createMockedClient([
            new Response(200, [], json_encode([
                'results' => [],
                'schema' => 'exdschema@1',
                'version' => 'abc123',
            ])),
        ], $history);

        $api->search()
            ->query('Name~"Potion"')
            ->sheets(['Item'])
            ->get();

        parse_str($history[0]['request']->getUri()->getQuery(), $params);
        expect($params['query'])->toBe('Name~"Potion"');
    });

    it('supports single sheet', function () {
        $history = [];
        $api = createMockedClient([
            new Response(200, [], json_encode([
                'results' => [],
                'schema' => 'exdschema@1',
                'version' => 'abc123',
            ])),
        ], $history);

        $api->search()
            ->query('Name~"Potion"')
            ->sheets(['Item'])
            ->get();

        parse_str($history[0]['request']->getUri()->getQuery(), $params);
        expect($params['sheets'])->toBe('Item');
    });

    it('supports multiple sheets', function () {
        $history = [];
        $api = createMockedClient([
            new Response(200, [], json_encode([
                'results' => [],
                'schema' => 'exdschema@1',
                'version' => 'abc123',
            ])),
        ], $history);

        $api->search()
            ->query('Name~"Potion"')
            ->sheets(['Item', 'Action', 'Recipe'])
            ->get();

        parse_str($history[0]['request']->getUri()->getQuery(), $params);
        expect($params['sheets'])->toBe('Item,Action,Recipe');
    });

    it('parses search response correctly', function () {
        $api = createMockedClient([
            new Response(200, [], json_encode([
                'results' => [
                    [
                        'score' => 1.5,
                        'sheet' => 'Item',
                        'row_id' => 4554,
                        'fields' => ['Name' => 'Potion'],
                    ],
                    [
                        'score' => 1.2,
                        'sheet' => 'Item',
                        'row_id' => 4555,
                        'fields' => ['Name' => 'Hi-Potion'],
                    ],
                ],
                'schema' => 'exdschema@1',
                'version' => 'abc123',
                'next' => 'abc-123-cursor',
            ])),
        ]);

        $response = $api->search()
            ->query('Name~"Potion"')
            ->sheets(['Item'])
            ->get();

        expect($response)->toBeInstanceOf(SearchResponse::class)
            ->and($response->results)->toHaveCount(2)
            ->and($response->results[0])->toBeInstanceOf(SearchResult::class)
            ->and($response->results[0]->score)->toBe(1.5)
            ->and($response->results[0]->sheet)->toBe('Item')
            ->and($response->results[0]->rowId)->toBe(4554)
            ->and($response->results[0]->fields['Name'])->toBe('Potion')
            ->and($response->schema)->toBe('exdschema@1')
            ->and($response->version)->toBe('abc123')
            ->and($response->next)->toBe('abc-123-cursor')
            ->and($response->hasMore())->toBeTrue();
    });

    it('supports cursor pagination', function () {
        $history = [];
        $api = createMockedClient([
            new Response(200, [], json_encode([
                'results' => [],
                'schema' => 'exdschema@1',
                'version' => 'abc123',
            ])),
        ], $history);

        $api->search()
            ->cursor('abc-123-cursor')
            ->get();

        parse_str($history[0]['request']->getUri()->getQuery(), $params);
        expect($params)->toHaveKey('cursor')
            ->and($params['cursor'])->toBe('abc-123-cursor')
            ->and($params)->not->toHaveKey('query')
            ->and($params)->not->toHaveKey('sheets');
    });

    it('throws without query or cursor', function () {
        $api = createMockedClient([]);

        $api->search()->sheets(['Item'])->get();
    })->throws(XivApiException::class, 'Search requires either a cursor or both query and sheets');

    it('throws without sheets', function () {
        $api = createMockedClient([]);

        $api->search()->query('Name~"test"')->get();
    })->throws(XivApiException::class, 'Search requires either a cursor or both query and sheets');

    it('hasMore returns false when no cursor', function () {
        $api = createMockedClient([
            new Response(200, [], json_encode([
                'results' => [],
                'schema' => 'exdschema@1',
                'version' => 'abc123',
            ])),
        ]);

        $response = $api->search()
            ->query('Name~"test"')
            ->sheets(['Item'])
            ->get();

        expect($response->hasMore())->toBeFalse();
    });

    it('accepts SearchQuery builder', function () {
        $history = [];
        $api = createMockedClient([
            new Response(200, [], json_encode([
                'results' => [],
                'schema' => 'exdschema@1',
                'version' => 'abc123',
            ])),
        ], $history);

        $query = SearchQuery::make()
            ->where('ClassJobCategory.PCT')->equals(true)
            ->where('ClassJobLevel')->greaterOrEqual(90);

        $api->search()
            ->query($query)
            ->sheets(['Action'])
            ->get();

        parse_str($history[0]['request']->getUri()->getQuery(), $params);
        expect($params['query'])->toBe('+ClassJobCategory.PCT=true +ClassJobLevel>=90');
    });
});

<?php

declare(strict_types=1);

use XivApi\Contracts\Arrayable;
use XivApi\Response\Row;
use XivApi\Response\RowResponse;
use XivApi\Response\SearchResponse;
use XivApi\Response\SearchResult;
use XivApi\Response\SheetListResponse;
use XivApi\Response\SheetResponse;
use XivApi\Response\Version;
use XivApi\Response\VersionsResponse;

describe('Version', function () {
    it('implements Arrayable', function () {
        $version = new Version('abc123', ['7.0', 'latest']);

        expect($version)->toBeInstanceOf(Arrayable::class);
    });

    it('converts to array correctly', function () {
        $version = new Version('abc123', ['7.0', 'latest']);

        expect($version->toArray())->toBe([
            'key' => 'abc123',
            'names' => ['7.0', 'latest'],
        ]);
    });

    it('round-trips through fromArray and toArray', function () {
        $data = ['key' => 'abc123', 'names' => ['7.0', 'latest']];
        $version = Version::fromArray($data);

        expect($version->toArray())->toBe($data);
    });
});

describe('VersionsResponse', function () {
    it('implements Arrayable', function () {
        $response = new VersionsResponse([]);

        expect($response)->toBeInstanceOf(Arrayable::class);
    });

    it('converts to array correctly', function () {
        $response = new VersionsResponse([
            new Version('abc123', ['7.0']),
            new Version('def456', ['7.01', 'latest']),
        ]);

        expect($response->toArray())->toBe([
            'versions' => [
                ['key' => 'abc123', 'names' => ['7.0']],
                ['key' => 'def456', 'names' => ['7.01', 'latest']],
            ],
        ]);
    });

    it('round-trips through fromArray and toArray', function () {
        $data = [
            'versions' => [
                ['key' => 'abc123', 'names' => ['7.0']],
                ['key' => 'def456', 'names' => ['7.01', 'latest']],
            ],
        ];
        $response = VersionsResponse::fromArray($data);

        expect($response->toArray())->toBe($data);
    });
});

describe('Row', function () {
    it('implements Arrayable', function () {
        $row = new Row(1, null, []);

        expect($row)->toBeInstanceOf(Arrayable::class);
    });

    it('converts to array correctly', function () {
        $row = new Row(
            rowId: 42,
            subrowId: 3,
            fields: ['Name' => 'Test Item', 'Level' => 50],
            transient: ['Icon' => '/icon/123.png'],
        );

        expect($row->toArray())->toBe([
            'row_id' => 42,
            'subrow_id' => 3,
            'fields' => ['Name' => 'Test Item', 'Level' => 50],
            'transient' => ['Icon' => '/icon/123.png'],
        ]);
    });

    it('converts to array with null values', function () {
        $row = new Row(
            rowId: 1,
            subrowId: null,
            fields: ['Name' => 'Test'],
            transient: null,
        );

        expect($row->toArray())->toBe([
            'row_id' => 1,
            'subrow_id' => null,
            'fields' => ['Name' => 'Test'],
            'transient' => null,
        ]);
    });

    it('round-trips through fromArray and toArray', function () {
        $data = [
            'row_id' => 42,
            'subrow_id' => 3,
            'fields' => ['Name' => 'Test Item'],
            'transient' => ['Icon' => '/icon/123.png'],
        ];
        $row = Row::fromArray($data);

        expect($row->toArray())->toBe($data);
    });
});

describe('SearchResult', function () {
    it('implements Arrayable through Row', function () {
        $result = new SearchResult(1.5, 'Item', 1, null, []);

        expect($result)->toBeInstanceOf(Arrayable::class);
    });

    it('converts to array correctly', function () {
        $result = new SearchResult(
            score: 2.5,
            sheet: 'Item',
            rowId: 42,
            subrowId: null,
            fields: ['Name' => 'Test Item'],
            transient: ['Icon' => '/icon/123.png'],
        );

        expect($result->toArray())->toBe([
            'score' => 2.5,
            'sheet' => 'Item',
            'row_id' => 42,
            'subrow_id' => null,
            'fields' => ['Name' => 'Test Item'],
            'transient' => ['Icon' => '/icon/123.png'],
        ]);
    });

    it('round-trips through fromArray and toArray', function () {
        $data = [
            'score' => 2.5,
            'sheet' => 'Item',
            'row_id' => 42,
            'subrow_id' => null,
            'fields' => ['Name' => 'Test Item'],
            'transient' => null,
        ];
        $result = SearchResult::fromArray($data);

        expect($result->toArray())->toBe($data);
    });
});

describe('SheetListResponse', function () {
    it('implements Arrayable', function () {
        $response = new SheetListResponse([]);

        expect($response)->toBeInstanceOf(Arrayable::class);
    });

    it('converts to array correctly', function () {
        $response = new SheetListResponse(['Item', 'Action', 'Mount']);

        expect($response->toArray())->toBe([
            'sheets' => [
                ['name' => 'Item'],
                ['name' => 'Action'],
                ['name' => 'Mount'],
            ],
        ]);
    });

    it('round-trips through fromArray and toArray', function () {
        $data = [
            'sheets' => [
                ['name' => 'Item'],
                ['name' => 'Action'],
            ],
        ];
        $response = SheetListResponse::fromArray($data);

        expect($response->toArray())->toBe($data);
    });
});

describe('SheetResponse', function () {
    it('implements Arrayable', function () {
        $response = new SheetResponse([], 'exdschema@abc', '7.0@def');

        expect($response)->toBeInstanceOf(Arrayable::class);
    });

    it('converts to array correctly', function () {
        $response = new SheetResponse(
            rows: [
                new Row(1, null, ['Name' => 'Item 1'], null),
                new Row(2, null, ['Name' => 'Item 2'], null),
            ],
            schema: 'exdschema@abc123',
            version: '7.0@def456',
        );

        expect($response->toArray())->toBe([
            'rows' => [
                ['row_id' => 1, 'subrow_id' => null, 'fields' => ['Name' => 'Item 1'], 'transient' => null],
                ['row_id' => 2, 'subrow_id' => null, 'fields' => ['Name' => 'Item 2'], 'transient' => null],
            ],
            'schema' => 'exdschema@abc123',
            'version' => '7.0@def456',
        ]);
    });

    it('round-trips through fromArray and toArray', function () {
        $data = [
            'rows' => [
                ['row_id' => 1, 'subrow_id' => null, 'fields' => ['Name' => 'Item 1'], 'transient' => null],
                ['row_id' => 2, 'subrow_id' => 0, 'fields' => ['Name' => 'Item 2'], 'transient' => ['Icon' => '/i.png']],
            ],
            'schema' => 'exdschema@abc123',
            'version' => '7.0@def456',
        ];
        $response = SheetResponse::fromArray($data);

        expect($response->toArray())->toBe($data);
    });
});

describe('RowResponse', function () {
    it('implements Arrayable', function () {
        $response = new RowResponse(1, null, [], null, 'schema', 'version');

        expect($response)->toBeInstanceOf(Arrayable::class);
    });

    it('converts to array correctly', function () {
        $response = new RowResponse(
            rowId: 42,
            subrowId: 3,
            fields: ['Name' => 'Test Item', 'Level' => 50],
            transient: ['Icon' => '/icon/123.png'],
            schema: 'exdschema@abc123',
            version: '7.0@def456',
        );

        expect($response->toArray())->toBe([
            'row_id' => 42,
            'subrow_id' => 3,
            'fields' => ['Name' => 'Test Item', 'Level' => 50],
            'transient' => ['Icon' => '/icon/123.png'],
            'schema' => 'exdschema@abc123',
            'version' => '7.0@def456',
        ]);
    });

    it('round-trips through fromArray and toArray', function () {
        $data = [
            'row_id' => 42,
            'subrow_id' => null,
            'fields' => ['Name' => 'Test Item'],
            'transient' => null,
            'schema' => 'exdschema@abc123',
            'version' => '7.0@def456',
        ];
        $response = RowResponse::fromArray($data);

        expect($response->toArray())->toBe($data);
    });
});

describe('SearchResponse', function () {
    it('implements Arrayable', function () {
        $response = new SearchResponse([], 'schema', 'version');

        expect($response)->toBeInstanceOf(Arrayable::class);
    });

    it('converts to array correctly', function () {
        $response = new SearchResponse(
            results: [
                new SearchResult(2.5, 'Item', 1, null, ['Name' => 'Sword'], null),
                new SearchResult(1.8, 'Item', 2, null, ['Name' => 'Shield'], null),
            ],
            schema: 'exdschema@abc123',
            version: '7.0@def456',
            next: 'cursor_token',
        );

        expect($response->toArray())->toBe([
            'results' => [
                ['score' => 2.5, 'sheet' => 'Item', 'row_id' => 1, 'subrow_id' => null, 'fields' => ['Name' => 'Sword'], 'transient' => null],
                ['score' => 1.8, 'sheet' => 'Item', 'row_id' => 2, 'subrow_id' => null, 'fields' => ['Name' => 'Shield'], 'transient' => null],
            ],
            'schema' => 'exdschema@abc123',
            'version' => '7.0@def456',
            'next' => 'cursor_token',
        ]);
    });

    it('converts to array with null cursor', function () {
        $response = new SearchResponse([], 'schema', 'version', null);

        expect($response->toArray())->toBe([
            'results' => [],
            'schema' => 'schema',
            'version' => 'version',
            'next' => null,
        ]);
    });

    it('round-trips through fromArray and toArray', function () {
        $data = [
            'results' => [
                ['score' => 2.5, 'sheet' => 'Item', 'row_id' => 1, 'subrow_id' => null, 'fields' => ['Name' => 'Sword'], 'transient' => null],
            ],
            'schema' => 'exdschema@abc123',
            'version' => '7.0@def456',
            'next' => 'cursor_token',
        ];
        $response = SearchResponse::fromArray($data);

        expect($response->toArray())->toBe($data);
    });
});

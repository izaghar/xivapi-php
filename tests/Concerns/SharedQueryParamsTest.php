<?php

declare(strict_types=1);

use GuzzleHttp\Psr7\Response;
use XivApi\Enums\Language;
use XivApi\Enums\Transform;
use XivApi\Query\Field;

use function XivApi\Tests\createMockedClient;

require_once __DIR__.'/../TestCase.php';

// Responses for mocking
function sheetResponse(): Response
{
    return new Response(200, [], json_encode([
        'rows' => [],
        'schema' => 'exdschema@1',
        'version' => 'abc123',
    ]));
}

function rowResponse(): Response
{
    return new Response(200, [], json_encode([
        'row_id' => 1,
        'fields' => [],
        'schema' => 'exdschema@1',
        'version' => 'abc123',
    ]));
}

function searchResponse(): Response
{
    return new Response(200, [], json_encode([
        'results' => [],
        'schema' => 'exdschema@1',
        'version' => 'abc123',
    ]));
}

function sheetListResponse(): Response
{
    return new Response(200, [], json_encode([
        'sheets' => [],
    ]));
}

function binaryResponse(): Response
{
    return new Response(200, [], 'binary-data');
}

// Clients with HasFields, HasLanguage, HasSchema, HasTransient
dataset('clientsWithFields', [
    'SheetRowsClient' => [
        fn (&$history) => createMockedClient([sheetResponse()], $history)->sheet('Item'),
        fn ($client) => $client->get(),
    ],
    'SheetRowClient' => [
        fn (&$history) => createMockedClient([rowResponse()], $history)->sheet('Item')->row(1),
        fn ($client) => $client->get(),
    ],
    'SearchClient' => [
        fn (&$history) => createMockedClient([searchResponse()], $history)->search()->query('Name~"test"')->sheets(['Item']),
        fn ($client) => $client->get(),
    ],
]);

// Clients with HasLimit (SheetRowClient does NOT have limit)
dataset('clientsWithLimit', [
    'SheetRowsClient' => [
        fn (&$history) => createMockedClient([sheetResponse()], $history)->sheet('Item'),
        fn ($client) => $client->get(),
    ],
    'SearchClient' => [
        fn (&$history) => createMockedClient([searchResponse()], $history)->search()->query('Name~"test"')->sheets(['Item']),
        fn ($client) => $client->get(),
    ],
]);

// Clients with HasVersion
dataset('clientsWithVersion', [
    'SheetRowsClient' => [
        fn (&$history) => createMockedClient([sheetResponse()], $history)->sheet('Item'),
        fn ($client) => $client->get(),
    ],
    'SheetRowClient' => [
        fn (&$history) => createMockedClient([rowResponse()], $history)->sheet('Item')->row(1),
        fn ($client) => $client->get(),
    ],
    'SearchClient' => [
        fn (&$history) => createMockedClient([searchResponse()], $history)->search()->query('Name~"test"')->sheets(['Item']),
        fn ($client) => $client->get(),
    ],
    'SheetIndexClient' => [
        fn (&$history) => createMockedClient([sheetListResponse()], $history)->sheetIndex(),
        fn ($client) => $client->list(),
    ],
    'AssetClient' => [
        fn (&$history) => createMockedClient([binaryResponse()], $history)->asset('ui/icon/000000/000001.tex'),
        fn ($client) => $client->get(),
    ],
    'MapAssetClient' => [
        fn (&$history) => createMockedClient([binaryResponse()], $history)->map('s1d1', '00'),
        fn ($client) => $client->get(),
    ],
]);

describe('HasVersion', function () {
    it('includes version parameter', function ($createClient, $execute) {
        $history = [];
        $client = $createClient($history);
        $execute($client->version('7.0'));

        parse_str($history[0]['request']->getUri()->getQuery(), $params);
        expect($params['version'])->toBe('7.0');
    })->with('clientsWithVersion');
});

describe('HasLimit', function () {
    it('includes limit parameter', function ($createClient, $execute) {
        $history = [];
        $client = $createClient($history);
        $execute($client->limit(50));

        parse_str($history[0]['request']->getUri()->getQuery(), $params);
        expect($params['limit'])->toBe('50');
    })->with('clientsWithLimit');
});

describe('HasLanguage', function () {
    it('includes language parameter', function ($createClient, $execute) {
        $history = [];
        $client = $createClient($history);
        $execute($client->language(Language::German));

        parse_str($history[0]['request']->getUri()->getQuery(), $params);
        expect($params['language'])->toBe('de');
    })->with('clientsWithFields');
});

describe('HasSchema', function () {
    it('includes schema parameter', function ($createClient, $execute) {
        $history = [];
        $client = $createClient($history);
        $execute($client->schema('exdschema@2'));

        parse_str($history[0]['request']->getUri()->getQuery(), $params);
        expect($params['schema'])->toBe('exdschema@2');
    })->with('clientsWithFields');
});

describe('HasFields', function () {
    it('includes fields as string', function ($createClient, $execute) {
        $history = [];
        $client = $createClient($history);
        $execute($client->fields('Name,Description'));

        parse_str($history[0]['request']->getUri()->getQuery(), $params);
        expect($params['fields'])->toBe('Name,Description');
    })->with('clientsWithFields');

    it('includes fields as Field object', function ($createClient, $execute) {
        $history = [];
        $client = $createClient($history);
        $execute($client->fields(Field::make('Name')->lang(Language::German)));

        parse_str($history[0]['request']->getUri()->getQuery(), $params);
        expect($params['fields'])->toBe('Name@lang(de)');
    })->with('clientsWithFields');

    it('includes fields as array of Field objects', function ($createClient, $execute) {
        $history = [];
        $client = $createClient($history);
        $execute($client->fields([
            Field::make('Name'),
            Field::make('Description')->as(Transform::Html),
        ]));

        parse_str($history[0]['request']->getUri()->getQuery(), $params);
        expect($params['fields'])->toBe('Name,Description@as(html)');
    })->with('clientsWithFields');

    it('includes fields as array of strings', function ($createClient, $execute) {
        $history = [];
        $client = $createClient($history);
        $execute($client->fields(['Name', 'Icon']));

        parse_str($history[0]['request']->getUri()->getQuery(), $params);
        expect($params['fields'])->toBe('Name,Icon');
    })->with('clientsWithFields');

    it('includes fields as mixed array', function ($createClient, $execute) {
        $history = [];
        $client = $createClient($history);
        $execute($client->fields([
            Field::make('Name')->lang(Language::German),
            'Icon',
            Field::make('Description')->as(Transform::Html),
        ]));

        parse_str($history[0]['request']->getUri()->getQuery(), $params);
        expect($params['fields'])->toBe('Name@lang(de),Icon,Description@as(html)');
    })->with('clientsWithFields');
});

describe('HasTransient', function () {
    it('includes transient as string', function ($createClient, $execute) {
        $history = [];
        $client = $createClient($history);
        $execute($client->transient('Description'));

        parse_str($history[0]['request']->getUri()->getQuery(), $params);
        expect($params['transient'])->toBe('Description');
    })->with('clientsWithFields');

    it('includes transient as Field object', function ($createClient, $execute) {
        $history = [];
        $client = $createClient($history);
        $execute($client->transient(Field::make('Description')->as(Transform::Html)));

        parse_str($history[0]['request']->getUri()->getQuery(), $params);
        expect($params['transient'])->toBe('Description@as(html)');
    })->with('clientsWithFields');

    it('includes transient as array of Field objects', function ($createClient, $execute) {
        $history = [];
        $client = $createClient($history);
        $execute($client->transient([
            Field::make('Description'),
            Field::make('Help')->as(Transform::Html),
        ]));

        parse_str($history[0]['request']->getUri()->getQuery(), $params);
        expect($params['transient'])->toBe('Description,Help@as(html)');
    })->with('clientsWithFields');

    it('includes transient as array of strings', function ($createClient, $execute) {
        $history = [];
        $client = $createClient($history);
        $execute($client->transient(['Description', 'Help']));

        parse_str($history[0]['request']->getUri()->getQuery(), $params);
        expect($params['transient'])->toBe('Description,Help');
    })->with('clientsWithFields');

    it('includes transient as mixed array', function ($createClient, $execute) {
        $history = [];
        $client = $createClient($history);
        $execute($client->transient([
            Field::make('Description')->as(Transform::Html),
            'Help',
        ]));

        parse_str($history[0]['request']->getUri()->getQuery(), $params);
        expect($params['transient'])->toBe('Description@as(html),Help');
    })->with('clientsWithFields');
});

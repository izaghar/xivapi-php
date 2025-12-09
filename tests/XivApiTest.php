<?php

declare(strict_types=1);

use GuzzleHttp\Psr7\Response;
use XivApi\Enums\Language;
use XivApi\Query\Field;

use function XivApi\Tests\createMockedClient;

require_once __DIR__.'/TestCase.php';

describe('global language()', function () {
    it('passes language to sheet client', function () {
        $history = [];
        $api = createMockedClient([
            new Response(200, [], json_encode([
                'rows' => [],
                'schema' => 'exdschema@1',
                'version' => 'abc123',
            ])),
        ], $history);

        $api->language(Language::German)->sheet('Item')->get();

        parse_str($history[0]['request']->getUri()->getQuery(), $params);
        expect($params['language'])->toBe('de');
    });

    it('passes language to search client', function () {
        $history = [];
        $api = createMockedClient([
            new Response(200, [], json_encode([
                'results' => [],
                'schema' => 'exdschema@1',
                'version' => 'abc123',
            ])),
        ], $history);

        $api->language(Language::French)->search()->query('Name~"Test"')->sheets(['Item'])->get();

        parse_str($history[0]['request']->getUri()->getQuery(), $params);
        expect($params['language'])->toBe('fr');
    });

    it('can be overridden per client', function () {
        $history = [];
        $api = createMockedClient([
            new Response(200, [], json_encode([
                'rows' => [],
                'schema' => 'exdschema@1',
                'version' => 'abc123',
            ])),
        ], $history);

        $api->language(Language::German)->sheet('Item')->language(Language::Japanese)->get();

        parse_str($history[0]['request']->getUri()->getQuery(), $params);
        expect($params['language'])->toBe('ja');
    });
});

describe('global gameVersion()', function () {
    it('passes version to sheet client', function () {
        $history = [];
        $api = createMockedClient([
            new Response(200, [], json_encode([
                'rows' => [],
                'schema' => 'exdschema@1',
                'version' => 'abc123',
            ])),
        ], $history);

        $api->gameVersion('7.0')->sheet('Item')->get();

        parse_str($history[0]['request']->getUri()->getQuery(), $params);
        expect($params['version'])->toBe('7.0');
    });

    it('passes version to sheetIndex client', function () {
        $history = [];
        $api = createMockedClient([
            new Response(200, [], json_encode([
                'sheets' => [],
            ])),
        ], $history);

        $api->gameVersion('7.0')->sheetIndex()->list();

        parse_str($history[0]['request']->getUri()->getQuery(), $params);
        expect($params['version'])->toBe('7.0');
    });

    it('passes version to asset client', function () {
        $history = [];
        $api = createMockedClient([
            new Response(200, [], 'binary-data'),
        ], $history);

        $api->gameVersion('7.0')->asset('ui/icon/000000/000001.tex')->get();

        parse_str($history[0]['request']->getUri()->getQuery(), $params);
        expect($params['version'])->toBe('7.0');
    });

    it('passes version to map client', function () {
        $history = [];
        $api = createMockedClient([
            new Response(200, [], 'binary-data'),
        ], $history);

        $api->gameVersion('7.0')->map('s1d1', '00')->get();

        parse_str($history[0]['request']->getUri()->getQuery(), $params);
        expect($params['version'])->toBe('7.0');
    });
});

describe('global schema()', function () {
    it('passes schema to sheet client', function () {
        $history = [];
        $api = createMockedClient([
            new Response(200, [], json_encode([
                'rows' => [],
                'schema' => 'exdschema@custom',
                'version' => 'abc123',
            ])),
        ], $history);

        $api->schema('exdschema@custom')->sheet('Item')->get();

        parse_str($history[0]['request']->getUri()->getQuery(), $params);
        expect($params['schema'])->toBe('exdschema@custom');
    });

    it('passes schema to search client', function () {
        $history = [];
        $api = createMockedClient([
            new Response(200, [], json_encode([
                'results' => [],
                'schema' => 'exdschema@custom',
                'version' => 'abc123',
            ])),
        ], $history);

        $api->schema('exdschema@custom')->search()->query('Name~"Test"')->sheets(['Item'])->get();

        parse_str($history[0]['request']->getUri()->getQuery(), $params);
        expect($params['schema'])->toBe('exdschema@custom');
    });
});

describe('global localizations()', function () {
    it('expands localized fields with global languages', function () {
        $history = [];
        $api = createMockedClient([
            new Response(200, [], json_encode([
                'rows' => [],
                'schema' => 'exdschema@1',
                'version' => 'abc123',
            ])),
        ], $history);

        $api->localizations(Language::German, Language::French)
            ->sheet('Item')
            ->fields([
                Field::make('Name')->localized(),
            ])
            ->get();

        parse_str($history[0]['request']->getUri()->getQuery(), $params);
        expect($params['fields'])->toBe('Name,Name@lang(de),Name@lang(fr)');
    });

    it('passes localizations to search client', function () {
        $history = [];
        $api = createMockedClient([
            new Response(200, [], json_encode([
                'results' => [],
                'schema' => 'exdschema@1',
                'version' => 'abc123',
            ])),
        ], $history);

        $api->localizations(Language::Japanese)
            ->search()
            ->query('Name~"Test"')
            ->sheets(['Item'])
            ->fields([
                Field::make('Name')->localized(),
            ])
            ->get();

        parse_str($history[0]['request']->getUri()->getQuery(), $params);
        expect($params['fields'])->toBe('Name,Name@lang(ja)');
    });
});

describe('combined global configuration', function () {
    it('passes all settings to client', function () {
        $history = [];
        $api = createMockedClient([
            new Response(200, [], json_encode([
                'rows' => [],
                'schema' => 'exdschema@custom',
                'version' => '7.0',
            ])),
        ], $history);

        $api->language(Language::German)
            ->gameVersion('7.0')
            ->schema('exdschema@custom')
            ->sheet('Item')
            ->get();

        parse_str($history[0]['request']->getUri()->getQuery(), $params);
        expect($params['language'])->toBe('de')
            ->and($params['version'])->toBe('7.0')
            ->and($params['schema'])->toBe('exdschema@custom');
    });
});

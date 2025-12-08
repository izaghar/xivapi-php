<?php

declare(strict_types=1);

use GuzzleHttp\Psr7\Response;
use XivApi\Enums\AssetFormat;
use XivApi\Exception\XivApiException;

use function XivApi\Tests\createMockedClient;

require_once __DIR__.'/../TestCase.php';

describe('asset()->get()', function () {
    it('builds correct URL with path and format', function () {
        $history = [];
        $api = createMockedClient([
            new Response(200, ['Content-Type' => 'image/png'], 'fake-png-data'),
        ], $history);

        $api->asset('ui/icon/051000/051474_hr1.tex')->get();

        $uri = $history[0]['request']->getUri();
        expect($uri->getPath())->toBe('/api/asset');

        parse_str($uri->getQuery(), $params);
        expect($params['path'])->toBe('ui/icon/051000/051474_hr1.tex')
            ->and($params['format'])->toBe('png');
    });

    it('returns binary content', function () {
        $binaryData = random_bytes(100);
        $api = createMockedClient([
            new Response(200, ['Content-Type' => 'image/png'], $binaryData),
        ]);

        $content = $api->asset('ui/icon/051000/051474_hr1.tex')->get();

        expect($content)->toBe($binaryData);
    });

    it('supports different formats', function () {
        $history = [];
        $api = createMockedClient([
            new Response(200, [], 'data'),
            new Response(200, [], 'data'),
            new Response(200, [], 'data'),
        ], $history);

        $api->asset('test.tex')->get();
        $api->asset('test.tex', AssetFormat::Jpg)->get();
        $api->asset('test.tex', AssetFormat::Webp)->get();

        parse_str($history[0]['request']->getUri()->getQuery(), $params1);
        parse_str($history[1]['request']->getUri()->getQuery(), $params2);
        parse_str($history[2]['request']->getUri()->getQuery(), $params3);

        expect($params1['format'])->toBe('png')
            ->and($params2['format'])->toBe('jpg')
            ->and($params3['format'])->toBe('webp');
    });

    it('returns correct URL', function () {
        $api = createMockedClient([]);

        $url = $api->asset('ui/icon/051000/051474_hr1.tex')->getUrl();

        expect($url)->toContain('https://v2.xivapi.com/api/asset')
            ->and($url)->toContain('path=')
            ->and($url)->toContain('format=png');
    });

    it('throws exception on error response', function () {
        $api = createMockedClient([
            new Response(400, [], json_encode([
                'code' => 400,
                'message' => 'invalid path',
            ])),
        ]);

        $api->asset('invalid/path')->get();
    })->throws(XivApiException::class, 'invalid path');
});

describe('asset()->fetch()', function () {
    it('returns PSR-7 response', function () {
        $api = createMockedClient([
            new Response(200, [
                'Content-Type' => 'image/png',
                'ETag' => '"abc123"',
            ], 'png-data'),
        ]);

        $response = $api->asset('test.tex')->fetch();

        expect($response->getStatusCode())->toBe(200)
            ->and($response->getHeaderLine('Content-Type'))->toBe('image/png')
            ->and($response->getHeaderLine('ETag'))->toBe('"abc123"');
    });
});

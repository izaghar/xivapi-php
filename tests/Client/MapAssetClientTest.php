<?php

declare(strict_types=1);

use GuzzleHttp\Psr7\Response;
use XivApi\Exception\XivApiException;

use function XivApi\Tests\createMockedClient;

require_once __DIR__.'/../TestCase.php';

describe('map()->get()', function () {
    it('builds correct URL with territory and index', function () {
        $history = [];
        $api = createMockedClient([
            new Response(200, ['Content-Type' => 'image/jpeg'], 'fake-jpg-data'),
        ], $history);

        $api->map('s1d1', '00')->get();

        $uri = $history[0]['request']->getUri();
        expect($uri->getPath())->toBe('/api/asset/map/s1d1/00');
    });

    it('returns binary content', function () {
        $binaryData = random_bytes(100);
        $api = createMockedClient([
            new Response(200, ['Content-Type' => 'image/jpeg'], $binaryData),
        ]);

        $content = $api->map('s1d1', '00')->get();

        expect($content)->toBe($binaryData);
    });

    it('returns correct URL', function () {
        $api = createMockedClient([]);

        $url = $api->map('s1d1', '00')->getUrl();

        expect($url)->toBe('https://v2.xivapi.com/api/asset/map/s1d1/00');
    });

    it('throws exception on error response', function () {
        $api = createMockedClient([
            new Response(404, [], json_encode([
                'code' => 404,
                'message' => 'map not found',
            ])),
        ]);

        $api->map('invalid', '99')->get();
    })->throws(XivApiException::class, 'map not found');
});

describe('map()->fetch()', function () {
    it('returns PSR-7 response', function () {
        $api = createMockedClient([
            new Response(200, [
                'Content-Type' => 'image/jpeg',
                'ETag' => '"map123"',
            ], 'jpg-data'),
        ]);

        $response = $api->map('s1d1', '00')->fetch();

        expect($response->getStatusCode())->toBe(200)
            ->and($response->getHeaderLine('Content-Type'))->toBe('image/jpeg')
            ->and($response->getHeaderLine('ETag'))->toBe('"map123"');
    });
});

<?php

/**
 * Example: Fetch a composed map
 *
 * Shows how to fetch map images. Maps are automatically composed from split source files.
 *
 * URL: https://v2.xivapi.com/api/asset/map/s1d1/00
 */

declare(strict_types=1);

$api = require __DIR__.'/support/bootstrap.php';

$client = $api->map('s1d1', '00');

echo 'URL: '.urldecode($client->getUrl())."\n";
echo '     '.$client->getUrl()."\n\n";

$content = $client->get();

$file = __DIR__.'/downloads/map-s1d1-00.jpg';
file_put_contents($file, $content);

echo "Map saved to: $file\n";
echo 'Size: '.number_format(strlen($content))." bytes\n";

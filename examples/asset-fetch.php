<?php

/**
 * Example: Fetch a game asset
 *
 * Shows how to fetch game assets (icons, textures) and convert them to usable formats.
 *
 * URL: https://v2.xivapi.com/api/asset?path=ui/loadingimage/-nowloading_base01_hr1.tex&format=png
 */

declare(strict_types=1);

use XivApi\Enums\AssetFormat;

$api = require __DIR__.'/support/bootstrap.php';

$client = $api->asset('ui/loadingimage/-nowloading_base01_hr1.tex', AssetFormat::Png);

echo 'URL: '.urldecode($client->getUrl())."\n";
echo '     '.$client->getUrl()."\n\n";

$content = $client->get();

$file = __DIR__.'/downloads/loading-screen.png';
file_put_contents($file, $content);

echo "Asset saved to: $file\n";
echo 'Size: '.number_format(strlen($content))." bytes\n";

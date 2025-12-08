<?php

/**
 * Example: List all available game versions
 *
 * Shows how to use the version endpoint to get all available game versions.
 *
 * URL: https://v2.xivapi.com/api/version
 */

declare(strict_types=1);

$api = require __DIR__.'/support/bootstrap.php';

$client = $api->version();

echo 'URL: '.urldecode($client->getUrl())."\n";
echo '     '.$client->getUrl()."\n\n";

$response = $client->list();

echo "Available versions:\n";
foreach ($response->versions as $version) {
    echo "  - $version->key: ".implode(', ', $version->names)."\n";
}

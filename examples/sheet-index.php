<?php

/**
 * Example: List all available sheets
 *
 * Shows how to use the sheets endpoint to get all available data sheets.
 *
 * URL: https://v2.xivapi.com/api/sheet
 */

declare(strict_types=1);

$api = require __DIR__.'/support/bootstrap.php';

$client = $api->sheetIndex();

echo 'URL: '.urldecode($client->getUrl())."\n";
echo '     '.$client->getUrl()."\n\n";

$response = $client->list();

echo 'Available sheets ('.count($response->sheets)." total):\n";
foreach (array_slice($response->sheets, 0, 20) as $sheet) {
    echo "  - $sheet\n";
}

if (count($response->sheets) > 20) {
    echo '  ... and '.(count($response->sheets) - 20)." more\n";
}

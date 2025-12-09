<?php

/**
 * Example: Pagination
 *
 * Docs: https://v2.xivapi.com/docs/guides/search/
 *
 * query=+Name~"rainbow"&limit=2
 */

declare(strict_types=1);

use XivApi\Query\SearchQuery;

$api = require __DIR__.'/support/bootstrap.php';

$client = $api->search()
    ->query(
        SearchQuery::where('Name', '~', 'rainbow')
    )
    ->sheets(['Item'])
    ->fields('Name')
    ->limit(2);

echo "query=+Name~\"rainbow\"&limit=2\n";
echo 'URL: '.$client->getUrl()."\n\n";

// First page
$response = $client->get();
$page = 1;

echo "Page $page:\n";
foreach ($response->results as $result) {
    echo "  - {$result->fields['Name']}\n";
}

// Next pages via cursor
while ($response->hasMore() && $page < 3) {
    $page++;
    $response = $api->search()->cursor($response->next)->get();

    echo "\nPage $page:\n";
    foreach ($response->results as $result) {
        echo "  - {$result->fields['Name']}\n";
    }
}

if ($response->hasMore()) {
    echo "\n... more pages available\n";
}

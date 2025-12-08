<?php

/**
 * Example: Multiple sheets
 *
 * Docs: https://v2.xivapi.com/docs/guides/search/
 *
 * sheets=Action,Item&query=Name~"rainbow"
 */

declare(strict_types=1);

use XivApi\Query\SearchQuery;

$api = require __DIR__.'/support/bootstrap.php';

$client = $api->search()
    ->query(
        SearchQuery::on('Name')->contains('rainbow')
    )
    ->sheets(['Action', 'Item'])
    ->fields('Name')
    ->limit(10);

echo "sheets=Action,Item&query=Name~\"rainbow\"\n";
echo 'URL: '.$client->getUrl()."\n\n";

$response = $client->get();
foreach ($response->results as $result) {
    echo "  [$result->sheet] {$result->fields['Name']}\n";
}

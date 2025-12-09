<?php

/**
 * Example: Nested fields
 *
 * Docs: https://v2.xivapi.com/docs/guides/search/
 *
 * query=ClassJob.Abbreviation="PCT"
 */

declare(strict_types=1);

use XivApi\Query\SearchQuery;

$api = require __DIR__.'/support/bootstrap.php';

$client = $api->search()
    ->query(
        SearchQuery::where('ClassJob.Abbreviation', 'PCT')
    )
    ->sheets(['Action'])
    ->fields('Name,ClassJob.Abbreviation')
    ->limit(10);

echo "query=ClassJob.Abbreviation=\"PCT\"\n";
echo 'URL: '.$client->getUrl()."\n\n";

$response = $client->get();
foreach ($response->results as $result) {
    echo "  - {$result->fields['Name']}\n";
}

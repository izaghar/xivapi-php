<?php

/**
 * Example: Basic queries
 *
 * Docs: https://v2.xivapi.com/docs/guides/search/
 *
 * query=Name="Rainbow Drip"
 * query=Name~"rainbow"
 * query=Recast100ms>3000
 */

declare(strict_types=1);

use XivApi\Query\SearchQuery;

$api = require __DIR__.'/support/bootstrap.php';

// Exact match: Name="Rainbow Drip"
$client = $api->search()
    ->query(
        SearchQuery::on('Name')->equals('Rainbow Drip')
    )
    ->sheets(['Action'])
    ->fields('Name');

echo "query=Name=\"Rainbow Drip\"\n";
echo 'URL: '.$client->getUrl()."\n\n";

$response = $client->get();
foreach ($response->results as $result) {
    echo "  - {$result->fields['Name']}\n";
}

echo "\n".str_repeat('-', 60)."\n\n";

// Partial match: Name~"rainbow"
$client = $api->search()
    ->query(
        SearchQuery::on('Name')->contains('rainbow')
    )
    ->sheets(['Item'])
    ->fields('Name')
    ->limit(5);

echo "query=Name~\"rainbow\"\n";
echo 'URL: '.$client->getUrl()."\n\n";

$response = $client->get();
foreach ($response->results as $result) {
    echo "  - {$result->fields['Name']}\n";
}

echo "\n".str_repeat('-', 60)."\n\n";

// Numeric comparison: Recast100ms>3000
$client = $api->search()
    ->query(
        SearchQuery::on('Recast100ms')->greaterThan(3000)
    )
    ->sheets(['Action'])
    ->fields('Name,Recast100ms')
    ->limit(5);

echo "query=Recast100ms>3000\n";
echo 'URL: '.$client->getUrl()."\n\n";

$response = $client->get();
foreach ($response->results as $result) {
    echo "  - {$result->fields['Name']} ({$result->fields['Recast100ms']})\n";
}

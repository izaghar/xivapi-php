<?php

/**
 * Example: Multiple clauses and relevancy
 *
 * Docs: https://v2.xivapi.com/docs/guides/search/
 *
 * query=ClassJobLevel=92 Name="Rainbow Drip"
 */

declare(strict_types=1);

use XivApi\Query\SearchQuery;

$api = require __DIR__.'/support/bootstrap.php';

$client = $api->search()
    ->query(
        SearchQuery::on('ClassJobLevel')->equals(92)
            ->andOn('Name')->equals('Rainbow Drip')
    )
    ->sheets(['Action'])
    ->fields('Name,ClassJobLevel')
    ->limit(5);

echo "query=ClassJobLevel=92 Name=\"Rainbow Drip\"\n";
echo 'URL: '.$client->getUrl()."\n\n";

$response = $client->get();
foreach ($response->results as $result) {
    $name = $result->fields['Name'];
    $level = $result->fields['ClassJobLevel'];
    echo "  [score: $result->score] $name (Level $level)\n";
}

<?php

/**
 * Example: Grouping clauses
 *
 * Docs: https://v2.xivapi.com/docs/guides/search/
 *
 * query=+ClassJobCategory.PCT=true +(ClassJobLevel=80 ClassJobLevel=90)
 */

declare(strict_types=1);

use XivApi\Query\SearchQuery;

$api = require __DIR__.'/support/bootstrap.php';

$client = $api->search()
    ->query(
        SearchQuery::must()->on('ClassJobCategory')->on('PCT')->equals(true)
            ->andMustGroup(fn ($q) => $q
                ->on('ClassJobLevel')->equals(80)
                ->on('ClassJobLevel')->equals(90)
            )
    )
    ->sheets(['Action'])
    ->fields('Name,ClassJobLevel');

echo "query=+ClassJobCategory.PCT=true +(ClassJobLevel=80 ClassJobLevel=90)\n";
echo 'URL: '.$client->getUrl()."\n\n";

$response = $client->get();
foreach ($response->results as $result) {
    echo "  - {$result->fields['Name']} (Level {$result->fields['ClassJobLevel']})\n";
}

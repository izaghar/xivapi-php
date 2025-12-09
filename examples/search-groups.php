<?php

/**
 * Example: Grouping clauses (OR logic)
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
        SearchQuery::where('ClassJobCategory.PCT', true)
            ->whereGroup(fn ($q) => $q
                ->orWhere('ClassJobLevel', 80)
                ->orWhere('ClassJobLevel', 90)
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

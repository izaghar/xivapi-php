<?php

/**
 * Example: Array fields
 *
 * Docs: https://v2.xivapi.com/docs/guides/search/
 *
 * query=+BaseParam[].Name="Spell Speed"
 */

declare(strict_types=1);

use XivApi\Query\SearchQuery;

$api = require __DIR__.'/support/bootstrap.php';

$client = $api->search()
    ->query(
        SearchQuery::whereHas('BaseParam', fn ($q) => $q
            ->where('Name', 'Spell Speed')
        )
    )
    ->sheets(['Item'])
    ->fields('Name,BaseParam[].Name')
    ->limit(5);

echo "query=+BaseParam[].Name=\"Spell Speed\"\n";
echo 'URL: '.$client->getUrl()."\n\n";

$response = $client->get();
foreach ($response->results as $result) {
    echo "  - {$result->fields['Name']}\n";
}

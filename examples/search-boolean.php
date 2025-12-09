<?php

/**
 * Example: Boolean logic with +must and -mustNot
 *
 * Docs: https://v2.xivapi.com/docs/guides/search/
 *
 * query=+ClassJobCategory.PCT=true +ClassJobLevel=92
 * query=ClassJobCategory.WAR=true -ClassJobLevel<96
 */

declare(strict_types=1);

use XivApi\Query\SearchQuery;

$api = require __DIR__.'/support/bootstrap.php';

// +must: +ClassJobCategory.PCT=true +ClassJobLevel=92
$client = $api->search()
    ->query(
        SearchQuery::where('ClassJobCategory.PCT', true)
            ->where('ClassJobLevel', 92)
    )
    ->sheets(['Action'])
    ->fields('Name,ClassJobLevel');

echo "query=+ClassJobCategory.PCT=true +ClassJobLevel=92\n";
echo 'URL: '.$client->getUrl()."\n\n";

$response = $client->get();
foreach ($response->results as $result) {
    echo "  - {$result->fields['Name']} (Level {$result->fields['ClassJobLevel']})\n";
}

echo "\n".str_repeat('-', 60)."\n\n";

// -mustNot: ClassJobCategory.WAR=true -ClassJobLevel<96
$client = $api->search()
    ->query(
        SearchQuery::orWhere('ClassJobCategory.WAR', true)
            ->whereNot('ClassJobLevel', '<', 96)
    )
    ->sheets(['Action'])
    ->fields('Name,ClassJobLevel');

echo "query=ClassJobCategory.WAR=true -ClassJobLevel<96\n";
echo 'URL: '.$client->getUrl()."\n\n";

$response = $client->get();
foreach ($response->results as $result) {
    echo "  - {$result->fields['Name']} (Level {$result->fields['ClassJobLevel']})\n";
}

<?php

/**
 * Example: Localized search
 *
 * Docs: https://v2.xivapi.com/docs/guides/search/
 *
 * query=+Name@ja="天使の筆"
 */

declare(strict_types=1);

use XivApi\Enums\Language;
use XivApi\Query\SearchQuery;

$api = require __DIR__.'/support/bootstrap.php';

$client = $api->search()
    ->query(
        SearchQuery::where('Name')->localizedTo(Language::Japanese)->equals('天使の筆')
    )
    ->sheets(['Item'])
    ->fields('Name,Name@ja');

echo "query=+Name@ja=\"天使の筆\"\n";
echo 'URL: '.$client->getUrl()."\n\n";

$response = $client->get();
foreach ($response->results as $result) {
    $nameEn = $result->fields['Name'];
    $nameJa = $result->fields['Name@ja'] ?? $nameEn;
    echo "  - $nameJa ($nameEn)\n";
}

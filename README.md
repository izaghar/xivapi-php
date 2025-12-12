# XIVAPI PHP Client

A modern PHP client for [XIVAPI v2](https://v2.xivapi.com) — the Final Fantasy XIV game data API.

[![PHP 8.3+](https://img.shields.io/badge/PHP-8.3%2B-blue.svg)](https://www.php.net/)
[![License: MIT](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)
[![codecov](https://codecov.io/gh/izaghar/xivapi-php/graph/badge.svg?token=SK8GCL7F9J)](https://codecov.io/gh/izaghar/xivapi-php)

---

## Table of Contents

- [Introduction](#introduction)
- [Requirements](#requirements)
- [Installation](#installation)
- [Quick Start](#quick-start)
- [Global Configuration](#global-configuration)
- [API Reference](#api-reference)
  - [Game Versions](#game-versions)
  - [Sheet Index](#sheet-index)
  - [Sheet Rows](#sheet-rows)
  - [Single Row](#single-row)
  - [Search](#search)
  - [Assets](#assets)
  - [Map Assets](#map-assets)
- [Query Builders](#query-builders)
  - [Field Builder](#field-builder)
  - [SearchQuery Builder](#searchquery-builder)
- [Response Classes](#response-classes)
- [Error Handling](#error-handling)
- [Debugging](#debugging)
- [Enums Reference](#enums-reference)
- [Examples](#examples)
- [License](#license)

---

## Introduction

XIVAPI provides access to Final Fantasy XIV game data including items, actions, quests, and more. This library offers a clean, fluent PHP interface for the XIVAPI v2 endpoints.

**Features:**

- Full coverage of all XIVAPI v2 endpoints
- Fluent query builders for fields and search queries
- PSR-18 HTTP client compatibility (use any HTTP client you prefer)
- Strong typing with PHP 8.3 enums
- Global configuration for language, version, and schema

For the official API documentation, visit [v2.xivapi.com/docs](https://v2.xivapi.com/docs).

---

## Requirements

- **PHP 8.3** or higher
- A **PSR-18 HTTP client** (e.g., Guzzle, Symfony HttpClient)
- A **PSR-17 HTTP factory** (usually bundled with the HTTP client)

---

## Installation

Install via Composer:

```bash
composer require izaghar/xivapi-php
```

You also need a PSR-18 compatible HTTP client. Here are some options:

**With Guzzle:**

```bash
composer require guzzlehttp/guzzle
```

**With Symfony HttpClient:**

```bash
composer require symfony/http-client nyholm/psr7
```

---

## Quick Start

```php
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;
use XivApi\XivApi;
use XivApi\Enums\Language;

// Create the API client
$api = new XivApi(
    new Client(),
    new HttpFactory()
);

// Fetch an item
$item = $api->sheet('Item')->row(4)->get();
echo $item->fields['Name']; // "Wind Shard"

// Search for items
$results = $api->search()
    ->query('Name~"Potion"')
    ->sheets(['Item'])
    ->get();

foreach ($results->results as $result) {
    echo $result->fields['Name'] . "\n";
}
```

---

## Global Configuration

Configure default settings that apply to all requests:

```php
use XivApi\Enums\Language;

$api = (new XivApi($http, $factory))
    ->language(Language::German)           // Default language for all requests
    ->gameVersion('7.0')                   // Lock to a specific game version
    ->schema('exdschema@latest')           // Use a specific schema
    ->localizations(Language::English, Language::Japanese); // For field expansion
```

All settings are optional and can be overridden per-request.

### Configuration Methods

| Method                              | Description                                    |
|-------------------------------------|------------------------------------------------|
| `language(Language $lang)`          | Set the default language for text fields       |
| `gameVersion(string $version)`      | Lock requests to a specific game version       |
| `schema(string $schema)`            | Use a specific schema for field definitions    |
| `localizations(Language ...$langs)` | Languages used when expanding localized fields |

---

## API Reference

### Game Versions

List all available game versions.

```php
$response = $api->version()->get();

foreach ($response->versions as $version) {
    echo $version->key . ': ' . implode(', ', $version->names) . "\n";
}
// Output: "2139246928a48a9a: 7.01, latest"
```

**Endpoint:** `GET /version`

---

### Sheet Index

List all available sheets (Excel files containing game data).

```php
$response = $api->sheetIndex()->list();

foreach ($response->sheets as $sheetName) {
    echo $sheetName . "\n";
}
// Output: "Action", "Item", "Quest", ...
```

**Endpoint:** `GET /sheet`

**Available Methods:**

| Method               | Description                            |
|----------------------|----------------------------------------|
| `version(string $v)` | Use a specific game version            |
| `list()`             | Execute and return `SheetListResponse` |
| `getUrl()`           | Get the request URL (for debugging)    |

---

### Sheet Rows

Query multiple rows from a sheet.

```php
// Basic query
$items = $api->sheet('Item')
    ->fields('Name,Icon,Description')
    ->language(Language::English)
    ->limit(50)
    ->get();

foreach ($items->rows as $row) {
    echo $row->rowId . ': ' . $row->fields['Name'] . "\n";
}
```

**Endpoint:** `GET /sheet/{sheet}`

**Available Methods:**

| Method                                    | Description                          |
|-------------------------------------------|--------------------------------------|
| `fields(string\|Field\|array $fields)`    | Select specific fields               |
| `transient(string\|Field\|array $fields)` | Select fields from transient sheet   |
| `language(Language $lang)`                | Set the language                     |
| `schema(string $schema)`                  | Use a specific schema                |
| `version(string $v)`                      | Use a specific game version          |
| `limit(int $n)`                           | Limit number of rows returned        |
| `after(string\|int $rowId)`               | Pagination: fetch rows after this ID |
| `rows(array $ids)`                        | Fetch specific row IDs               |
| `get()`                                   | Execute and return `SheetResponse`   |
| `getUrl()`                                | Get the request URL (for debugging)  |

#### Pagination

```php
// First page
$page1 = $api->sheet('Item')->limit(100)->get();

// Next page (using the last row ID)
$lastRowId = end($page1->rows)->rowId;
$page2 = $api->sheet('Item')->limit(100)->after($lastRowId)->get();
```

#### Fetch Specific Rows

```php
$items = $api->sheet('Item')
    ->rows([1, 2, 3, 4, 5])
    ->get();
```

---

### Single Row

Fetch a single row by ID.

```php
$item = $api->sheet('Item')->row(4)->get();

echo $item->rowId;           // 4
echo $item->fields['Name'];  // "Wind Shard"
echo $item->version;         // "2139246928a48a9a"
echo $item->schema;          // "exdschema@..."
```

**Endpoint:** `GET /sheet/{sheet}/{row}`

**Available Methods:**

| Method                                    | Description                         |
|-------------------------------------------|-------------------------------------|
| `fields(string\|Field\|array $fields)`    | Select specific fields              |
| `transient(string\|Field\|array $fields)` | Select fields from transient sheet  |
| `language(Language $lang)`                | Set the language                    |
| `schema(string $schema)`                  | Use a specific schema               |
| `version(string $v)`                      | Use a specific game version         |
| `get()`                                   | Execute and return `RowResponse`    |
| `getUrl()`                                | Get the request URL (for debugging) |

#### Subrows

Some sheets have subrows (e.g., `262144:0`, `262144:1`). Access them with a string:

```php
$row = $api->sheet('GilShopItem')->row('262144:1')->get();

echo $row->rowId;    // 262144
echo $row->subrowId; // 1
```

---

### Search

Search across one or more sheets using a query string.

```php
$results = $api->search()
    ->query('Name~"Potion" +Level>=1')
    ->sheets(['Item'])
    ->fields('Name,Icon,LevelItem')
    ->limit(20)
    ->get();

foreach ($results->results as $result) {
    echo sprintf(
        "[%s] %s #%d: %s (score: %.2f)\n",
        $result->sheet,
        $result->rowId,
        $result->fields['Name'],
        $result->score
    );
}
```

**Endpoint:** `GET /search`

**Available Methods:**

| Method                                    | Description                         |
|-------------------------------------------|-------------------------------------|
| `query(string\|SearchQuery $q)`           | The search query                    |
| `sheets(array $sheets)`                   | Sheets to search in                 |
| `fields(string\|Field\|array $fields)`    | Select specific fields              |
| `transient(string\|Field\|array $fields)` | Select fields from transient sheet  |
| `language(Language $lang)`                | Set the language                    |
| `schema(string $schema)`                  | Use a specific schema               |
| `version(string $v)`                      | Use a specific game version         |
| `limit(int $n)`                           | Limit number of results             |
| `cursor(string $cursor)`                  | Continue from a previous search     |
| `get()`                                   | Execute and return `SearchResponse` |
| `getUrl()`                                | Get the request URL (for debugging) |

#### Search Query Syntax

```php
// Partial string match (contains)
'Name~"Potion"'

// Exact equality
'LevelItem=50'

// Numeric comparisons
'LevelItem>=50'
'LevelItem<10'

// Required clause (must match)
'+Name~"Potion"'

// Excluded clause (must not match)
'-Name~"Hi-Potion"'

// Nested fields
'ItemUICategory.Name="Medicine"'

// Language-specific
'Name@ja~"ポーション"'

// Array elements
'BaseParam[].Name="Strength"'

// Grouping
'+(Name~"Potion" Name~"Ether") +LevelItem>=1'
```

#### Cursor Pagination

```php
$page1 = $api->search()
    ->query('Name~"Potion"')
    ->sheets(['Item'])
    ->limit(20)
    ->get();

if ($page1->hasMore()) {
    $page2 = $api->search()
        ->cursor($page1->next)
        ->get();
}
```

---

### Assets

Fetch game assets (icons, textures) in various formats.

```php
// Get PNG image data
$imageData = $api->asset('ui/icon/051000/051474_hr1.tex')->get();
file_put_contents('icon.png', $imageData);

// Different format
use XivApi\Enums\AssetFormat;

$jpg = $api->asset('ui/loadingimage/title/ffxiv_logo_jpen.tex', AssetFormat::Jpg)->get();
```

**Endpoint:** `GET /asset`

**Available Methods:**

| Method               | Description                            |
|----------------------|----------------------------------------|
| `version(string $v)` | Use a specific game version            |
| `get()`              | Execute and return binary image data   |
| `fetch()`            | Execute and return full PSR-7 Response |
| `getUrl()`           | Get the request URL (for debugging)    |

#### Using the PSR-7 Response

```php
$response = $api->asset('ui/icon/051000/051474_hr1.tex')->fetch();

$etag = $response->getHeaderLine('ETag');
$contentType = $response->getHeaderLine('Content-Type');
$body = $response->getBody()->getContents();
```

---

### Map Assets

Fetch composed map images.

```php
// Get a map image (JPEG)
$mapData = $api->map('s1d1', '00')->get();
file_put_contents('map.jpg', $mapData);
```

**Endpoint:** `GET /asset/map/{territory}/{index}`

**Parameters:**

- `territory` — Territory ID (e.g., `s1d1`, `r1f1`)
- `index` — Map index, zero-padded (e.g., `00`, `01`)

**Available Methods:**

| Method               | Description                            |
|----------------------|----------------------------------------|
| `version(string $v)` | Use a specific game version            |
| `get()`              | Execute and return binary image data   |
| `fetch()`            | Execute and return full PSR-7 Response |
| `getUrl()`           | Get the request URL (for debugging)    |

---

## Query Builders

### Field Builder

For complex field selections, use the `Field` class:

```php
use XivApi\Query\Field;
use XivApi\Enums\Language;
use XivApi\Enums\Transform;

$api->sheet('Item')
    ->fields([
        Field::make('Name'),
        Field::make('Description')->as(Transform::Html),
        Field::make('ItemUICategory.Name'),
    ])
    ->get();
```

#### Field Methods

| Method                      | Description                  | Example Output                     |
|-----------------------------|------------------------------|------------------------------------|
| `make(string $name)`        | Create a field               | `Name`                             |
| `lang(Language $l)`         | Set explicit language        | `Name@lang(de)`                    |
| `as(Transform $t)`          | Apply transformation         | `Description@as(html)`             |
| `asRaw()`                   | Apply raw transformation     | `Icon@as(raw)`                     |
| `asHtml()`                  | Apply HTML transformation    | `Description@as(html)`             |
| `localized(Language ...$l)` | Expand to multiple languages | `Name,Name@lang(de),Name@lang(fr)` |

#### Localized Fields

Get a field in multiple languages at once:

```php
// Explicit languages
Field::make('Name')->localized(Language::German, Language::French)
// Builds: Name,Name@lang(de),Name@lang(fr)

// Use globally configured languages
$api = (new XivApi($http, $factory))
    ->localizations(Language::German, Language::French);

$api->sheet('Item')
    ->fields([
        Field::make('Name')->localized(), // Uses global languages
        Field::make('Icon'),              // Not localized
    ])
    ->get();
```

#### Transformations

```php
// HTML formatting for strings with markup
Field::make('Description')->as(Transform::Html)
// Or using the shortcut:
Field::make('Description')->asHtml()

// Raw value (skip relation resolution)
Field::make('ItemUICategory')->as(Transform::Raw)
// Or using the shortcut:
Field::make('ItemUICategory')->asRaw()
```

#### Nested Fields

Use dot-notation for nested fields:

```php
Field::make('ItemUICategory.Name')
Field::make('ItemUICategory.Name')->lang(Language::Japanese)
```

---

### SearchQuery Builder

Build search queries programmatically instead of writing raw query strings:

```php
use XivApi\Query\SearchQuery;
use XivApi\Enums\Language;

// Simple conditions
$query = SearchQuery::where('LevelItem', 50);
$query = SearchQuery::where('Name')->contains('Potion');

// Shortcuts
$query = SearchQuery::where('Name', 'Potion')       // equals
    ->where('Level', '>=', 90);                     // greaterOrEqual

// Chained conditions
$query = SearchQuery::where('Name')->contains('Potion')
    ->where('LevelItem')->greaterOrEqual(10)
    ->whereNot('Name')->contains('Hi-');

// Pass to search
$api->search()
    ->query($query)
    ->sheets(['Item'])
    ->get();
```

> **Note:** You can also use `SearchQuery::make()` to create an empty instance first.

#### Condition Methods

| Method             | Prefix | Description    |
|--------------------|--------|----------------|
| `where($field)`    | `+`    | Must match     |
| `whereNot($field)` | `-`    | Must not match |
| `orWhere($field)`  | (none) | OR / optional  |

#### Shortcuts

```php
->where('Field', $value)              // equals
->where('Field', '=', $value)         // equals
->where('Field', '~', $value)         // contains
->where('Field', '>', $value)         // greaterThan
->where('Field', '<', $value)         // lessThan
->where('Field', '>=', $value)        // greaterOrEqual
->where('Field', '<=', $value)        // lessOrEqual
```

#### Finisher Methods

| Method                    | Query Syntax    |
|---------------------------|-----------------|
| `equals($value)`          | `Field=value`   |
| `contains(string $value)` | `Field~"value"` |
| `greaterThan($value)`     | `Field>value`   |
| `lessThan($value)`        | `Field<value`   |
| `greaterOrEqual($value)`  | `Field>=value`  |
| `lessOrEqual($value)`     | `Field<=value`  |

#### Grouping

```php
// AND group (all conditions must match)
$query = SearchQuery::where('LevelItem')->greaterOrEqual(1)
    ->whereGroup(fn($g) => $g
        ->where('Name')->contains('Potion')
        ->where('Name')->contains('Ether')
    );
// Builds: +LevelItem>=1 +(+Name~"Potion" +Name~"Ether")

// OR group (at least one condition must match)
$query = SearchQuery::where('LevelItem')->greaterOrEqual(1)
    ->whereGroup(fn($g) => $g
        ->orWhere('Name')->contains('Potion')
        ->orWhere('Name')->contains('Ether')
    );
// Builds: +LevelItem>=1 +(Name~"Potion" Name~"Ether")

// Must not group
->whereNotGroup(fn($g) => ...)  // -(...)

// OR group
->orWhereGroup(fn($g) => ...)   // (...)
```

#### Array Fields

```php
// Search in array elements
$query = SearchQuery::whereHas('BaseParam', fn($q) => $q
    ->where('Name')->equals('Strength')
);
// Builds: +BaseParam[].Name="Strength"

// Variants
->whereHas($array, $callback)     // +Array[].Field=value
->whereHasNot($array, $callback)  // -Array[].Field=value
->orWhereHas($array, $callback)   // Array[].Field=value
```

#### Language Filter

```php
$query = SearchQuery::where('Name')->localizedTo(Language::Japanese)->contains('ポーション');
// Builds: +Name@ja~"ポーション"
```

#### Complex Example

```php
$query = SearchQuery::where('IsFlying', true)
    ->where('ExtraSeats', '>', 0)
    ->orWhere('Name')->localizedTo(Language::Japanese)->contains('ドラゴン')
    ->whereGroup(fn($q) => $q
        ->orWhere('Level', 80)
        ->orWhere('Level', 90)
    );
// Builds: +IsFlying=true +ExtraSeats>0 Name@ja~"ドラゴン" +(Level=80 Level=90)
```

---

## Response Classes

| Class               | Returned By             | Key Properties                                                  |
|---------------------|-------------------------|-----------------------------------------------------------------|
| `VersionsResponse`  | `version()->get()`      | `versions[]`                                                    |
| `Version`           | —                       | `key`, `names[]`                                                |
| `SheetListResponse` | `sheetIndex()->list()`  | `sheets[]`                                                      |
| `SheetResponse`     | `sheet()->get()`        | `rows[]`, `version`, `schema`                                   |
| `RowResponse`       | `sheet()->row()->get()` | `rowId`, `subrowId`, `fields`, `transient`, `version`, `schema` |
| `Row`               | —                       | `rowId`, `subrowId`, `fields`, `transient`                      |
| `SearchResponse`    | `search()->get()`       | `results[]`, `next`, `version`, `schema`                        |
| `SearchResult`      | —                       | `sheet`, `rowId`, `subrowId`, `score`, `fields`, `transient`    |

### Checking for More Results

```php
$response = $api->search()->query('...')->sheets(['Item'])->get();

if ($response->hasMore()) {
    // Use $response->next as cursor for next page
}
```

---

## Error Handling

All API errors throw `XivApiException`:

```php
use XivApi\Exception\XivApiException;

try {
    $api->sheet('InvalidSheet')->get();
} catch (XivApiException $e) {
    echo $e->getMessage();   // Error message from API
    echo $e->statusCode;     // HTTP status code (e.g., 404)
}
```

---

## Debugging

Every client has a `getUrl()` method to inspect the request URL:

```php
$url = $api->sheet('Item')
    ->fields('Name,Icon')
    ->language(Language::German)
    ->limit(10)
    ->getUrl();

echo $url;
// https://v2.xivapi.com/api/sheet/Item?fields=Name,Icon&language=de&limit=10
```

---

## Enums Reference

### Language

```php
use XivApi\Enums\Language;

Language::Japanese  // ja
Language::English   // en
Language::German    // de
Language::French    // fr
```

### Transform

```php
use XivApi\Enums\Transform;

Transform::Raw   // Skip relation resolution
Transform::Html  // Format string as HTML
```

### AssetFormat

```php
use XivApi\Enums\AssetFormat;

AssetFormat::Png   // PNG image (default)
AssetFormat::Jpg   // JPEG image
AssetFormat::Webp  // WebP image
```

---

## Examples

See the [examples/Readme.md](examples/README.md) for runnable code samples:

- **Versions** — List game versions
- **Sheets** — Query sheet data with pagination
- **Search** — Various search patterns
- **Assets** — Download icons and maps

---

## License

MIT License. See [LICENSE](LICENSE) for details.
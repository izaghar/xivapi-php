# Examples

Runnable code samples demonstrating the XIVAPI PHP client.

## Running Examples

```bash
cd examples
php versions.php
```

---

## Available Examples

### General

| File              | Description                      |
|-------------------|----------------------------------|
| `versions.php`    | List all available game versions |
| `sheet-index.php` | List all available sheets        |

### Assets

| File              | Description                                     |
|-------------------|-------------------------------------------------|
| `asset-fetch.php` | Download a game texture (loading screen) as PNG |
| `asset-map.php`   | Download a composed map image                   |

### Search

| File                         | Description                                              |
|------------------------------|----------------------------------------------------------|
| `search-basic.php`           | Basic queries: exact match, contains, numeric comparison |
| `search-boolean.php`         | Required (+) and excluded (-) clauses                    |
| `search-groups.php`          | Grouping clauses with parentheses                        |
| `search-nested.php`          | Querying nested/related fields                           |
| `search-array.php`           | Searching in array fields                                |
| `search-localized.php`       | Language-specific searches                               |
| `search-multiple-sheets.php` | Searching across multiple sheets                         |
| `search-pagination.php`      | Cursor-based pagination                                  |
| `search-relevancy.php`       | Understanding relevancy scores                           |

---

## Output Directory

Downloaded assets are saved to the `downloads/` directory.
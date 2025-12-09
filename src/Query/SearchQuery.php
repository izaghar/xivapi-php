<?php

declare(strict_types=1);

namespace XivApi\Query;

use XivApi\Query\Builder\SearchQueryBuilder;
use XivApi\Query\Builder\WhereBuilder;

/**
 * Factory for search query expressions.
 *
 * Example: SearchQuery::where('Name')->equals('Potion')
 *          SearchQuery::where('Name', 'Potion')
 *          SearchQuery::where('Level', '>=', 90)
 *
 * @method static SearchQueryBuilder|WhereBuilder where(string $field, string|int|float|bool|null $operatorOrValue = null, string|int|float|bool|null $value = null)
 * @method static SearchQueryBuilder|WhereBuilder whereNot(string $field, string|int|float|bool|null $operatorOrValue = null, string|int|float|bool|null $value = null)
 * @method static SearchQueryBuilder|WhereBuilder orWhere(string $field, string|int|float|bool|null $operatorOrValue = null, string|int|float|bool|null $value = null)
 * @method static SearchQueryBuilder whereGroup(callable $callback)
 * @method static SearchQueryBuilder whereNotGroup(callable $callback)
 * @method static SearchQueryBuilder orWhereGroup(callable $callback)
 * @method static SearchQueryBuilder whereHas(string $array, callable $callback)
 * @method static SearchQueryBuilder whereHasNot(string $array, callable $callback)
 * @method static SearchQueryBuilder orWhereHas(string $array, callable $callback)
 *
 * @see https://v2.xivapi.com/docs#search
 */
class SearchQuery
{
    /**
     * Create a new SearchQueryBuilder instance.
     */
    public static function make(): SearchQueryBuilder
    {
        return new SearchQueryBuilder;
    }

    /**
     * Forward static calls to a new builder instance.
     */
    public static function __callStatic(string $method, array $arguments): mixed
    {
        return self::make()->$method(...$arguments);
    }
}

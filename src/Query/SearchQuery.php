<?php

declare(strict_types=1);

namespace XivApi\Query;

use Stringable;
use XivApi\Contracts\ClauseCollector;
use XivApi\Query\Builder\WhereBuilder;
use XivApi\Query\Concerns\BuildsConditions;

/**
 * Fluent builder for search query expressions.
 *
 * Example: SearchQuery::where('Name')->equals('Potion')
 *          SearchQuery::where('Name', 'Potion')
 *          SearchQuery::where('Level', '>=', 90)
 *
 * @method static self|WhereBuilder where(string $field, string|int|float|bool|null $operatorOrValue = null, string|int|float|bool|null $value = null)
 * @method static self|WhereBuilder whereNot(string $field, string|int|float|bool|null $operatorOrValue = null, string|int|float|bool|null $value = null)
 * @method static self|WhereBuilder orWhere(string $field, string|int|float|bool|null $operatorOrValue = null, string|int|float|bool|null $value = null)
 * @method static self whereGroup(callable $callback)
 * @method static self whereNotGroup(callable $callback)
 * @method static self orWhereGroup(callable $callback)
 * @method static self whereHas(string $array, callable $callback)
 * @method static self whereHasNot(string $array, callable $callback)
 * @method static self orWhereHas(string $array, callable $callback)
 *
 * @see https://v2.xivapi.com/docs#search
 */
class SearchQuery implements ClauseCollector, Stringable
{
    use BuildsConditions;

    /**
     * Create a new SearchQuery instance.
     */
    public static function make(): self
    {
        return new self;
    }

    /**
     * Allow static calls to instance methods by forwarding to a new instance.
     */
    public static function __callStatic(string $method, array $arguments): mixed
    {
        return self::make()->$method(...$arguments);
    }

    /**
     * Add a raw clause string (used by fluent builders).
     */
    public function addClause(string $clause): self
    {
        $this->clauses[] = $clause;

        return $this;
    }

    public function __toString(): string
    {
        return implode(' ', $this->clauses);
    }
}

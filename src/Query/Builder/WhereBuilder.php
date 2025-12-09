<?php

declare(strict_types=1);

namespace XivApi\Query\Builder;

use XivApi\Contracts\ClauseCollector;
use XivApi\Enums\Language;
use XivApi\Query\Concerns\TerminatesClause;
use XivApi\Query\SearchQuery;

/**
 * Builder for field conditions.
 *
 * Provides terminators (equals, contains, etc.) and language filter.
 */
readonly class WhereBuilder
{
    use TerminatesClause;

    public function __construct(
        private string $prefix,
        private string $path,
        private ClauseCollector $collector,
    ) {}

    /**
     * Apply language filter.
     *
     * Example: Name@ja
     */
    public function localizedTo(Language $language): LocalizedWhereBuilder
    {
        return new LocalizedWhereBuilder(
            $this->prefix,
            $this->path.'@'.$language->value,
            $this->collector,
        );
    }

    private function terminate(string $operator, string|int|float|bool $value): SearchQuery|GroupBuilder|ArrayGroupBuilder
    {
        $formatted = self::formatValue($value);

        /** @var SearchQuery|GroupBuilder|ArrayGroupBuilder */
        return $this->collector->addClause($this->prefix.$this->path.$operator.$formatted);
    }

    public static function formatValue(string|int|float|bool $value): string
    {
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (is_string($value)) {
            return '"'.$value.'"';
        }

        return (string) $value;
    }
}

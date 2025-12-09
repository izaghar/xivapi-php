<?php

declare(strict_types=1);

namespace XivApi\Query\Builder;

use XivApi\Contracts\ClauseCollector;
use XivApi\Query\Concerns\TerminatesClause;

/**
 * Builder for localized field conditions.
 *
 * Only provides terminators (equals, contains, etc.) - no further localizedTo().
 */
readonly class LocalizedWhereBuilder
{
    use TerminatesClause;

    public function __construct(
        private string $prefix,
        private string $path,
        private ClauseCollector $collector,
    ) {}

    private function terminate(string $operator, string|int|float|bool $value): SearchQueryBuilder|GroupBuilder|ArrayGroupBuilder
    {
        $formatted = WhereBuilder::formatValue($value);

        /** @var SearchQueryBuilder|GroupBuilder|ArrayGroupBuilder */
        return $this->collector->addClause($this->prefix.$this->path.$operator.$formatted);
    }
}

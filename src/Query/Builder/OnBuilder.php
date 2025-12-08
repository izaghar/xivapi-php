<?php

declare(strict_types=1);

namespace XivApi\Query\Builder;

use XivApi\Enums\Language;

/**
 * Builder for field path and conditions.
 *
 * Extends TerminalOnBuilder with path modification methods.
 */
readonly class OnBuilder extends TerminalOnBuilder
{
    /**
     * Access a nested field.
     *
     * Example: ClassJobCategory.PCT
     */
    public function on(string $field): self
    {
        return new self(
            $this->prefix,
            $this->path.'.'.$field,
            $this->collector,
        );
    }

    /**
     * Apply language filter.
     *
     * Example: Name@ja
     *
     * Returns TerminalOnBuilder - no more path modifications allowed.
     */
    public function lang(Language $language): TerminalOnBuilder
    {
        return new TerminalOnBuilder(
            $this->prefix,
            $this->path.'@'.$language->value,
            $this->collector,
        );
    }
}

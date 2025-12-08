<?php

declare(strict_types=1);

namespace XivApi\Client\Concerns;

use XivApi\Enums\Language;

/**
 * Provides language configuration.
 */
trait HasLanguage
{
    private ?Language $language = null;

    /**
     * Set the language for field data.
     */
    public function language(Language $language): self
    {
        $this->language = $language;

        return $this;
    }
}

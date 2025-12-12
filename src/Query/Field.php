<?php

declare(strict_types=1);

namespace XivApi\Query;

use Stringable;
use XivApi\Enums\Language;
use XivApi\Enums\Transform;

/**
 * Builder for field filter expressions.
 *
 * Supports dot-notation for nested fields: Field::make('ItemUICategory.Name')
 *
 * @see https://v2.xivapi.com/docs#fields-filter
 */
class Field implements Stringable
{
    private ?Language $language = null;

    private ?Transform $transform = null;

    /** @var Language[]|null null = not localized, [] = use global, [...] = use these */
    private ?array $localizedLanguages = null;

    private function __construct(
        private readonly string $name,
    ) {}

    /**
     * Create a new field.
     */
    public static function make(string $name): self
    {
        return new self($name);
    }

    /**
     * Set an explicit language for this field.
     */
    public function lang(Language $language): self
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Mark this field as localized.
     *
     * Returns base field + localized variants.
     * If languages provided, uses those. Otherwise, uses globally configured languages.
     */
    public function localized(Language ...$languages): self
    {
        $this->localizedLanguages = $languages;

        return $this;
    }

    /**
     * Apply a transformation to this field.
     */
    public function as(Transform $transform): self
    {
        $this->transform = $transform;

        return $this;
    }

    /**
     * Apply the raw transformation to this field.
     *
     * Prevents processing of relationships and icons.
     */
    public function asRaw(): self
    {
        return $this->as(Transform::Raw);
    }

    /**
     * Apply the HTML transformation to this field.
     *
     * Formats string values into HTML fragments.
     */
    public function asHtml(): self
    {
        return $this->as(Transform::Html);
    }

    /**
     * Build field string(s).
     *
     * @param  Language[]  $globalLanguages  Languages from XivApi config
     * @return string[]
     */
    public function build(array $globalLanguages = []): array
    {
        $results = [];

        // Base field
        $results[] = $this->buildSingle();

        // Add localized variants if marked
        if ($this->localizedLanguages !== null && $this->language === null) {
            $languages = $this->localizedLanguages !== [] ? $this->localizedLanguages : $globalLanguages;

            foreach ($languages as $lang) {
                $results[] = $this->buildSingle($lang);
            }
        }

        return $results;
    }

    private function buildSingle(?Language $langOverride = null): string
    {
        $result = $this->name;

        $lang = $langOverride ?? $this->language;
        if ($lang !== null) {
            $result .= '@lang('.$lang->value.')';
        }

        if ($this->transform !== null) {
            $result .= '@as('.$this->transform->value.')';
        }

        return $result;
    }

    public function __toString(): string
    {
        return $this->buildSingle();
    }
}

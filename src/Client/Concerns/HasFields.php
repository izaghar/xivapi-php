<?php

declare(strict_types=1);

namespace XivApi\Client\Concerns;

use XivApi\Enums\Language;
use XivApi\Query\Field;

/**
 * Provides field and transient field filtering functionality.
 *
 * Transient sheets contain infrequently accessed data split from the main sheet.
 * For example, ActionTransient contains descriptions for the Action sheet.
 */
trait HasFields
{
    /** @var Language[] */
    private array $localizations = [];

    /** @var Field[] */
    private array $fields = [];

    /** @var Field[] */
    private array $transient = [];

    /**
     * Set the fields filter.
     *
     * Accepts strings, Field objects, or mixed arrays.
     *
     * @param  string|Field|array<Field|string>  $fields
     */
    public function fields(string|Field|array $fields): self
    {
        $this->fields = $this->parseFields($fields);

        return $this;
    }

    /**
     * Set the transient fields filter.
     *
     * Accepts the same syntax as fields(), including Field objects with decorators.
     *
     * @param  string|Field|array<Field|string>  $transient
     */
    public function transient(string|Field|array $transient): self
    {
        $this->transient = $this->parseFields($transient);

        return $this;
    }

    /**
     * @param  string|Field|array<Field|string>  $input
     * @return Field[]
     */
    private function parseFields(string|Field|array $input): array
    {
        if (is_string($input)) {
            return array_map(
                fn (string $f) => Field::make(trim($f)),
                explode(',', $input),
            );
        }

        if ($input instanceof Field) {
            return [$input];
        }

        return array_map(
            fn (Field|string $f) => $f instanceof Field ? $f : Field::make($f),
            $input,
        );
    }

    private function buildFieldsString(): ?string
    {
        return $this->buildFieldList($this->fields);
    }

    private function buildTransientString(): ?string
    {
        return $this->buildFieldList($this->transient);
    }

    /**
     * @param  Field[]  $fields
     */
    private function buildFieldList(array $fields): ?string
    {
        if ($fields === []) {
            return null;
        }

        $parts = [];
        foreach ($fields as $field) {
            $parts = [...$parts, ...$field->build($this->localizations)];
        }

        return implode(',', $parts);
    }
}

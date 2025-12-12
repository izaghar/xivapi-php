<?php

declare(strict_types=1);

use XivApi\Enums\Language;
use XivApi\Enums\Transform;
use XivApi\Query\Field;

describe('Field::make()', function () {
    it('creates a simple field', function () {
        $field = Field::make('Name');

        expect((string) $field)->toBe('Name')
            ->and($field->build())->toBe(['Name']);
    });
});

describe('Field::lang()', function () {
    it('adds language decorator', function () {
        $field = Field::make('Name')->lang(Language::German);

        expect((string) $field)->toBe('Name@lang(de)')
            ->and($field->build())->toBe(['Name@lang(de)']);
    });
});

describe('Field::as()', function () {
    it('adds raw transform', function () {
        $field = Field::make('Icon')->as(Transform::Raw);

        expect((string) $field)->toBe('Icon@as(raw)');
    });

    it('adds html transform', function () {
        $field = Field::make('Description')->as(Transform::Html);

        expect((string) $field)->toBe('Description@as(html)');
    });
});

describe('Field::asRaw()', function () {
    it('adds raw transform as shortcut', function () {
        $field = Field::make('Icon')->asRaw();

        expect((string) $field)->toBe('Icon@as(raw)')
            ->and($field->build())->toBe(['Icon@as(raw)']);
    });

    it('works with other modifiers', function () {
        $field = Field::make('Description')->lang(Language::Japanese)->asRaw();

        expect((string) $field)->toBe('Description@lang(ja)@as(raw)');
    });
});

describe('Field::asHtml()', function () {
    it('adds html transform as shortcut', function () {
        $field = Field::make('Description')->asHtml();

        expect((string) $field)->toBe('Description@as(html)')
            ->and($field->build())->toBe(['Description@as(html)']);
    });

    it('works with other modifiers', function () {
        $field = Field::make('Description')->lang(Language::German)->asHtml();

        expect((string) $field)->toBe('Description@lang(de)@as(html)');
    });
});

describe('Field::localized()', function () {
    it('expands with explicit languages', function () {
        $field = Field::make('Name')->localized(Language::German, Language::French);

        expect($field->build())->toBe([
            'Name',
            'Name@lang(de)',
            'Name@lang(fr)',
        ]);
    });

    it('expands with global languages', function () {
        $field = Field::make('Name')->localized();

        expect($field->build([Language::Japanese, Language::English]))->toBe([
            'Name',
            'Name@lang(ja)',
            'Name@lang(en)',
        ]);
    });

    it('prefers explicit over global languages', function () {
        $field = Field::make('Name')->localized(Language::French);

        expect($field->build([Language::Japanese, Language::English]))->toBe([
            'Name',
            'Name@lang(fr)',
        ]);
    });

    it('returns only base when no languages', function () {
        $field = Field::make('Name')->localized();

        expect($field->build())->toBe(['Name']);
    });
});

describe('nested fields with dot-notation', function () {
    it('supports dot-notation in field name', function () {
        $field = Field::make('ItemUICategory.Name');

        expect((string) $field)->toBe('ItemUICategory.Name');
    });

    it('combines dot-notation with language decorator', function () {
        $field = Field::make('ItemUICategory.Name')->lang(Language::German);

        expect((string) $field)->toBe('ItemUICategory.Name@lang(de)');
    });
});

describe('combined decorators', function () {
    it('combines lang and as', function () {
        $field = Field::make('Description')
            ->lang(Language::Japanese)
            ->as(Transform::Html);

        expect((string) $field)->toBe('Description@lang(ja)@as(html)');
    });
});

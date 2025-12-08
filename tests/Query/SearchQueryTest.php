<?php

declare(strict_types=1);

use XivApi\Enums\Language;
use XivApi\Query\SearchQuery;

describe('SearchQuery::on()', function () {
    it('creates equality clause', function () {
        $query = SearchQuery::on('Name')->equals('Potion');

        expect((string) $query)->toBe('Name="Potion"');
    });

    it('creates contains clause', function () {
        $query = SearchQuery::on('Name')->contains('rainbow');

        expect((string) $query)->toBe('Name~"rainbow"');
    });

    it('creates greater than clause', function () {
        $query = SearchQuery::on('Level')->greaterThan(90);

        expect((string) $query)->toBe('Level>90');
    });

    it('creates less than clause', function () {
        $query = SearchQuery::on('Level')->lessThan(50);

        expect((string) $query)->toBe('Level<50');
    });

    it('creates greater or equal clause', function () {
        $query = SearchQuery::on('Level')->greaterOrEqual(90);

        expect((string) $query)->toBe('Level>=90');
    });

    it('creates less or equal clause', function () {
        $query = SearchQuery::on('Level')->lessOrEqual(10);

        expect((string) $query)->toBe('Level<=10');
    });

    it('handles boolean true', function () {
        $query = SearchQuery::on('IsFlying')->equals(true);

        expect((string) $query)->toBe('IsFlying=true');
    });

    it('handles boolean false', function () {
        $query = SearchQuery::on('IsFlying')->equals(false);

        expect((string) $query)->toBe('IsFlying=false');
    });

    it('handles float values', function () {
        $query = SearchQuery::on('Value')->greaterThan(1.5);

        expect((string) $query)->toBe('Value>1.5');
    });
});

describe('nested fields with on()', function () {
    it('accesses nested field', function () {
        $query = SearchQuery::on('ClassJobCategory')->on('PCT')->equals(true);

        expect((string) $query)->toBe('ClassJobCategory.PCT=true');
    });

    it('chains multiple on calls', function () {
        $query = SearchQuery::on('A')->on('B')->on('C')->equals('value');

        expect((string) $query)->toBe('A.B.C="value"');
    });
});

describe('array fields with any()', function () {
    it('accesses array elements', function () {
        $query = SearchQuery::any('BaseParam')->on('Name')->equals('Spell Speed');

        expect((string) $query)->toBe('BaseParam[].Name="Spell Speed"');
    });

    it('combines array with nested field', function () {
        $query = SearchQuery::any('Items')->on('Category')->on('Name')->contains('Weapon');

        expect((string) $query)->toBe('Items[].Category.Name~"Weapon"');
    });
});

describe('language filter with lang()', function () {
    it('adds language decorator', function () {
        $query = SearchQuery::on('Name')->lang(Language::Japanese)->equals('ポーション');

        expect((string) $query)->toBe('Name@ja="ポーション"');
    });

    it('works with nested fields', function () {
        $query = SearchQuery::on('Item')->on('Name')->lang(Language::German)->contains('Trank');

        expect((string) $query)->toBe('Item.Name@de~"Trank"');
    });

    it('prevents further path modifications after lang', function () {
        // After lang() only terminators are available (equals, contains, etc.)
        // This is enforced by the type system - TerminalOnBuilder has no dot() or any()
        $query = SearchQuery::on('Name')->lang(Language::French)->equals('Potion');

        expect((string) $query)->toBe('Name@fr="Potion"');
    });
});

describe('SearchQuery::must()', function () {
    it('adds + prefix', function () {
        $query = SearchQuery::must()->on('Level')->greaterOrEqual(90);

        expect((string) $query)->toBe('+Level>=90');
    });

    it('works with nested fields', function () {
        $query = SearchQuery::must()->on('ClassJobCategory')->on('PCT')->equals(true);

        expect((string) $query)->toBe('+ClassJobCategory.PCT=true');
    });
});

describe('SearchQuery::mustNot()', function () {
    it('adds - prefix', function () {
        $query = SearchQuery::mustNot()->on('Level')->lessThan(50);

        expect((string) $query)->toBe('-Level<50');
    });
});

describe('chaining clauses', function () {
    it('chains with andOn()', function () {
        $query = SearchQuery::on('Name')->contains('Potion')
            ->andOn('Level')->greaterOrEqual(90);

        expect((string) $query)->toBe('Name~"Potion" Level>=90');
    });

    it('chains with andMust()', function () {
        $query = SearchQuery::on('Name')->contains('Potion')
            ->andMust()->on('Level')->greaterOrEqual(90);

        expect((string) $query)->toBe('Name~"Potion" +Level>=90');
    });

    it('chains with andMustNot()', function () {
        $query = SearchQuery::must()->on('IsFlying')->equals(true)
            ->andMustNot()->on('ExtraSeats')->equals(0);

        expect((string) $query)->toBe('+IsFlying=true -ExtraSeats=0');
    });

    it('chains multiple conditions', function () {
        $query = SearchQuery::must()->on('IsFlying')->equals(true)
            ->andMust()->on('ExtraSeats')->greaterThan(0)
            ->andOn('Name')->contains('Dragon');

        expect((string) $query)->toBe('+IsFlying=true +ExtraSeats>0 Name~"Dragon"');
    });
});

describe('grouping', function () {
    it('creates simple group', function () {
        $query = SearchQuery::group(fn ($q) => $q
            ->on('Level')->equals(80)
            ->on('Level')->equals(90)
        );

        expect((string) $query)->toBe('(Level=80 Level=90)');
    });

    it('creates group with must inside', function () {
        $query = SearchQuery::group(fn ($q) => $q
            ->must()->on('A')->equals(1)
            ->mustNot()->on('B')->equals(2)
        );

        expect((string) $query)->toBe('(+A=1 -B=2)');
    });

    it('chains group with andGroup', function () {
        $query = SearchQuery::on('Name')->contains('Potion')
            ->andGroup(fn ($q) => $q
                ->on('Level')->equals(80)
                ->on('Level')->equals(90)
            );

        expect((string) $query)->toBe('Name~"Potion" (Level=80 Level=90)');
    });

    it('chains group with andMustGroup', function () {
        $query = SearchQuery::must()->on('ClassJobCategory')->on('PCT')->equals(true)
            ->andMustGroup(fn ($q) => $q
                ->on('Level')->equals(80)
                ->on('Level')->equals(90)
            );

        expect((string) $query)->toBe('+ClassJobCategory.PCT=true +(Level=80 Level=90)');
    });

    it('chains group with andMustNotGroup', function () {
        $query = SearchQuery::on('Name')->contains('Potion')
            ->andMustNotGroup(fn ($q) => $q
                ->on('Name')->contains('Hi-')
                ->on('Name')->contains('Mega')
            );

        expect((string) $query)->toBe('Name~"Potion" -(Name~"Hi-" Name~"Mega")');
    });

    it('creates nested groups', function () {
        $query = SearchQuery::group(fn ($q) => $q
            ->on('A')->equals(1)
            ->group(fn ($inner) => $inner
                ->on('B')->equals(2)
                ->on('C')->equals(3)
            )
        );

        expect((string) $query)->toBe('(A=1 (B=2 C=3))');
    });
});

describe('complex queries', function () {
    it('builds mount search query', function () {
        $query = SearchQuery::must()->on('IsFlying')->equals(true)
            ->andMust()->on('ExtraSeats')->greaterThan(0)
            ->andOn('Name')->contains('Dragon');

        expect((string) $query)->toBe('+IsFlying=true +ExtraSeats>0 Name~"Dragon"');
    });

    it('builds item search with array field', function () {
        $query = SearchQuery::any('BaseParam')->on('Name')->equals('Spell Speed');

        expect((string) $query)->toBe('BaseParam[].Name="Spell Speed"');
    });

    it('builds localized search', function () {
        $query = SearchQuery::on('Name')->lang(Language::Japanese)->contains('ポーション');

        expect((string) $query)->toBe('Name@ja~"ポーション"');
    });
});

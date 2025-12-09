<?php

declare(strict_types=1);

use XivApi\Enums\Language;
use XivApi\Query\SearchQuery;

describe('SearchQuery static method forwarding', function () {
    it('forwards where() via __callStatic', function () {
        $query = SearchQuery::where('Name')->equals('Potion');

        expect((string) $query)->toBe('+Name="Potion"');
    });

    it('forwards whereNot() via __callStatic', function () {
        $query = SearchQuery::whereNot('Name')->equals('Potion');

        expect((string) $query)->toBe('-Name="Potion"');
    });

    it('forwards orWhere() via __callStatic', function () {
        $query = SearchQuery::orWhere('Name')->equals('Potion');

        expect((string) $query)->toBe('Name="Potion"');
    });

    it('forwards whereGroup() via __callStatic', function () {
        $query = SearchQuery::whereGroup(fn ($q) => $q->where('Name', 'Test'));

        expect((string) $query)->toBe('+(+Name="Test")');
    });

    it('forwards whereHas() via __callStatic', function () {
        $query = SearchQuery::whereHas('Items', fn ($q) => $q->where('Name', 'Test'));

        expect((string) $query)->toBe('+Items[].Name="Test"');
    });
});

describe('SearchQuery::where() - must conditions (+)', function () {
    it('creates equality clause with + prefix', function () {
        $query = SearchQuery::make()->where('Name')->equals('Potion');

        expect((string) $query)->toBe('+Name="Potion"');
    });

    it('creates contains clause with + prefix', function () {
        $query = SearchQuery::make()->where('Name')->contains('rainbow');

        expect((string) $query)->toBe('+Name~"rainbow"');
    });

    it('creates greater than clause', function () {
        $query = SearchQuery::make()->where('Level')->greaterThan(90);

        expect((string) $query)->toBe('+Level>90');
    });

    it('creates less than clause', function () {
        $query = SearchQuery::make()->where('Level')->lessThan(50);

        expect((string) $query)->toBe('+Level<50');
    });

    it('creates greater or equal clause', function () {
        $query = SearchQuery::make()->where('Level')->greaterOrEqual(90);

        expect((string) $query)->toBe('+Level>=90');
    });

    it('creates less or equal clause', function () {
        $query = SearchQuery::make()->where('Level')->lessOrEqual(10);

        expect((string) $query)->toBe('+Level<=10');
    });

    it('handles boolean true', function () {
        $query = SearchQuery::make()->where('IsFlying')->equals(true);

        expect((string) $query)->toBe('+IsFlying=true');
    });

    it('handles boolean false', function () {
        $query = SearchQuery::make()->where('IsFlying')->equals(false);

        expect((string) $query)->toBe('+IsFlying=false');
    });

    it('handles float values', function () {
        $query = SearchQuery::make()->where('Value')->greaterThan(1.5);

        expect((string) $query)->toBe('+Value>1.5');
    });
});

describe('SearchQuery::whereNot() - must not conditions (-)', function () {
    it('adds - prefix', function () {
        $query = SearchQuery::make()->whereNot('Level')->lessThan(50);

        expect((string) $query)->toBe('-Level<50');
    });
});

describe('SearchQuery::orWhere() - optional conditions (no prefix)', function () {
    it('creates clause without prefix', function () {
        $query = SearchQuery::make()->orWhere('Name')->contains('Dragon');

        expect((string) $query)->toBe('Name~"Dragon"');
    });
});

describe('nested fields with dot notation', function () {
    it('accesses nested field', function () {
        $query = SearchQuery::make()->where('ClassJobCategory.PCT')->equals(true);

        expect((string) $query)->toBe('+ClassJobCategory.PCT=true');
    });

    it('chains multiple dots', function () {
        $query = SearchQuery::make()->where('A.B.C')->equals('value');

        expect((string) $query)->toBe('+A.B.C="value"');
    });
});

describe('array fields with whereHas()', function () {
    it('accesses array elements with + prefix', function () {
        $query = SearchQuery::make()->whereHas('BaseParam', fn ($q) => $q
            ->where('Name')->equals('Spell Speed')
        );

        expect((string) $query)->toBe('+BaseParam[].Name="Spell Speed"');
    });

    it('combines array with nested field', function () {
        $query = SearchQuery::make()->whereHas('Items', fn ($q) => $q
            ->where('Category.Name')->contains('Weapon')
        );

        expect((string) $query)->toBe('+Items[].Category.Name~"Weapon"');
    });

    it('supports multiple conditions in array', function () {
        $query = SearchQuery::make()->whereHas('Items', fn ($q) => $q
            ->where('Category')->equals('Weapon')
            ->where('Level')->greaterThan(50)
        );

        expect((string) $query)->toBe('+Items[].Category="Weapon" +Items[].Level>50');
    });
});

describe('language filter with localizedTo()', function () {
    it('adds language decorator', function () {
        $query = SearchQuery::make()->where('Name')->localizedTo(Language::Japanese)->equals('ポーション');

        expect((string) $query)->toBe('+Name@ja="ポーション"');
    });

    it('works with nested fields', function () {
        $query = SearchQuery::make()->where('Item.Name')->localizedTo(Language::German)->contains('Trank');

        expect((string) $query)->toBe('+Item.Name@de~"Trank"');
    });

    it('works with contains', function () {
        $query = SearchQuery::make()->where('Name')->localizedTo(Language::French)->contains('Potion');

        expect((string) $query)->toBe('+Name@fr~"Potion"');
    });
});

describe('chaining clauses', function () {
    it('chains multiple where conditions', function () {
        $query = SearchQuery::make()
            ->where('Name')->contains('Potion')
            ->where('Level')->greaterOrEqual(90);

        expect((string) $query)->toBe('+Name~"Potion" +Level>=90');
    });

    it('chains where with whereNot', function () {
        $query = SearchQuery::make()
            ->where('IsFlying')->equals(true)
            ->whereNot('ExtraSeats')->equals(0);

        expect((string) $query)->toBe('+IsFlying=true -ExtraSeats=0');
    });

    it('chains where with orWhere', function () {
        $query = SearchQuery::make()
            ->where('IsFlying')->equals(true)
            ->orWhere('Name')->contains('Dragon');

        expect((string) $query)->toBe('+IsFlying=true Name~"Dragon"');
    });

    it('chains multiple conditions', function () {
        $query = SearchQuery::make()
            ->where('IsFlying')->equals(true)
            ->where('ExtraSeats')->greaterThan(0)
            ->orWhere('Name')->contains('Dragon');

        expect((string) $query)->toBe('+IsFlying=true +ExtraSeats>0 Name~"Dragon"');
    });
});

describe('grouping with whereGroup() - must groups (+)', function () {
    it('creates must group with + prefix', function () {
        $query = SearchQuery::make()->whereGroup(fn ($q) => $q
            ->where('Level')->equals(80)
            ->where('Level')->equals(90)
        );

        expect((string) $query)->toBe('+(+Level=80 +Level=90)');
    });

    it('creates group with mixed prefixes inside', function () {
        $query = SearchQuery::make()->whereGroup(fn ($q) => $q
            ->where('A')->equals(1)
            ->whereNot('B')->equals(2)
        );

        expect((string) $query)->toBe('+(+A=1 -B=2)');
    });

    it('chains group after condition', function () {
        $query = SearchQuery::make()
            ->where('Name')->contains('Potion')
            ->whereGroup(fn ($q) => $q
                ->where('Level')->equals(80)
                ->where('Level')->equals(90)
            );

        expect((string) $query)->toBe('+Name~"Potion" +(+Level=80 +Level=90)');
    });

    it('chains whereNotGroup after condition', function () {
        $query = SearchQuery::make()
            ->where('Name')->contains('Potion')
            ->whereNotGroup(fn ($q) => $q
                ->where('Name')->contains('Hi-')
                ->where('Name')->contains('Mega')
            );

        expect((string) $query)->toBe('+Name~"Potion" -(+Name~"Hi-" +Name~"Mega")');
    });

    it('creates nested groups', function () {
        $query = SearchQuery::make()->whereGroup(fn ($q) => $q
            ->where('A')->equals(1)
            ->whereGroup(fn ($inner) => $inner
                ->where('B')->equals(2)
                ->where('C')->equals(3)
            )
        );

        expect((string) $query)->toBe('+(+A=1 +(+B=2 +C=3))');
    });
});

describe('grouping with whereNotGroup() - must not groups (-)', function () {
    it('creates must not group with - prefix', function () {
        $query = SearchQuery::make()->whereNotGroup(fn ($q) => $q
            ->where('Name')->contains('Hi-')
            ->where('Name')->contains('Mega')
        );

        expect((string) $query)->toBe('-(+Name~"Hi-" +Name~"Mega")');
    });
});

describe('grouping with orWhereGroup() - optional groups (no prefix)', function () {
    it('creates optional group without prefix', function () {
        $query = SearchQuery::make()->orWhereGroup(fn ($q) => $q
            ->where('Level')->equals(80)
            ->where('Level')->equals(90)
        );

        expect((string) $query)->toBe('(+Level=80 +Level=90)');
    });
});

describe('whereHas variants', function () {
    it('whereHas uses + prefix', function () {
        $query = SearchQuery::make()->whereHas('BaseParam', fn ($q) => $q
            ->where('Name')->equals('Strength')
        );

        expect((string) $query)->toBe('+BaseParam[].Name="Strength"');
    });

    it('whereHasNot uses - prefix', function () {
        $query = SearchQuery::make()->whereHasNot('Items', fn ($q) => $q
            ->where('Category')->equals('Weapon')
        );

        expect((string) $query)->toBe('-Items[].Category="Weapon"');
    });

    it('orWhereHas uses no prefix', function () {
        $query = SearchQuery::make()->orWhereHas('BaseParam', fn ($q) => $q
            ->where('Name')->equals('Strength')
        );

        expect((string) $query)->toBe('BaseParam[].Name="Strength"');
    });

    it('supports chained whereHas', function () {
        $query = SearchQuery::make()
            ->where('Name')->contains('Sword')
            ->whereHas('BaseParam', fn ($q) => $q
                ->where('Name')->equals('Strength')
            );

        expect((string) $query)->toBe('+Name~"Sword" +BaseParam[].Name="Strength"');
    });
});

describe('static group entry points', function () {
    it('whereGroup creates must group (+)', function () {
        $query = SearchQuery::make()->whereGroup(fn ($g) => $g
            ->where('A')->equals(1)
            ->where('B')->equals(2)
        );

        expect((string) $query)->toBe('+(+A=1 +B=2)');
    });

    it('whereNotGroup creates must not group (-)', function () {
        $query = SearchQuery::make()->whereNotGroup(fn ($g) => $g
            ->where('Name')->contains('Hi-')
            ->where('Name')->contains('Mega')
        );

        expect((string) $query)->toBe('-(+Name~"Hi-" +Name~"Mega")');
    });

    it('orWhereGroup creates optional group (no prefix)', function () {
        $query = SearchQuery::make()->orWhereGroup(fn ($g) => $g
            ->where('A')->equals(1)
            ->where('B')->equals(2)
        );

        expect((string) $query)->toBe('(+A=1 +B=2)');
    });

});

describe('GroupBuilder chaining', function () {
    it('supports whereHas inside groups', function () {
        $query = SearchQuery::make()->whereGroup(fn ($g) => $g
            ->whereHas('BaseParam', fn ($q) => $q
                ->where('Name')->equals('Strength')
            )
            ->where('Level')->greaterThan(50)
        );

        expect((string) $query)->toBe('+(+BaseParam[].Name="Strength" +Level>50)');
    });

    it('supports whereNot inside groups', function () {
        $query = SearchQuery::make()->whereGroup(fn ($g) => $g
            ->where('Name')->contains('Potion')
            ->whereNot('Name')->contains('Hi-')
        );

        expect((string) $query)->toBe('+(+Name~"Potion" -Name~"Hi-")');
    });

    it('supports orWhere inside groups', function () {
        $query = SearchQuery::make()->whereGroup(fn ($g) => $g
            ->where('A')->equals(1)
            ->orWhere('B')->equals(2)
        );

        expect((string) $query)->toBe('+(+A=1 B=2)');
    });

    it('supports whereGroup inside groups', function () {
        $query = SearchQuery::make()->whereGroup(fn ($g) => $g
            ->where('A')->equals(1)
            ->whereGroup(fn ($inner) => $inner
                ->where('B')->equals(2)
                ->where('C')->equals(3)
            )
        );

        expect((string) $query)->toBe('+(+A=1 +(+B=2 +C=3))');
    });

    it('supports whereNotGroup inside groups', function () {
        $query = SearchQuery::make()->whereGroup(fn ($g) => $g
            ->where('Name')->contains('Potion')
            ->whereNotGroup(fn ($inner) => $inner
                ->where('Name')->contains('Hi-')
                ->where('Name')->contains('Mega')
            )
        );

        expect((string) $query)->toBe('+(+Name~"Potion" -(+Name~"Hi-" +Name~"Mega"))');
    });

    it('supports orWhereGroup inside groups', function () {
        $query = SearchQuery::make()->whereGroup(fn ($g) => $g
            ->where('A')->equals(1)
            ->orWhereGroup(fn ($inner) => $inner
                ->where('B')->equals(2)
                ->where('C')->equals(3)
            )
        );

        expect((string) $query)->toBe('+(+A=1 (+B=2 +C=3))');
    });

});

describe('shortcuts', function () {
    it('where with value shortcut for equals', function () {
        $query = SearchQuery::make()->where('Name', 'Potion');

        expect((string) $query)->toBe('+Name="Potion"');
    });

    it('where with operator and value shortcut', function () {
        $query = SearchQuery::make()->where('Level', '>=', 90);

        expect((string) $query)->toBe('+Level>=90');
    });

    it('whereNot with value shortcut', function () {
        $query = SearchQuery::make()->whereNot('Category', 'Weapon');

        expect((string) $query)->toBe('-Category="Weapon"');
    });

    it('orWhere with contains shortcut', function () {
        $query = SearchQuery::make()->orWhere('Name', '~', 'Dragon');

        expect((string) $query)->toBe('Name~"Dragon"');
    });

    it('shortcuts work in groups', function () {
        $query = SearchQuery::make()->whereGroup(fn ($g) => $g
            ->where('A', 1)
            ->whereNot('B', '>', 10)
        );

        expect((string) $query)->toBe('+(+A=1 -B>10)');
    });

    it('all operators work', function () {
        $query = SearchQuery::make()
            ->where('A', '=', 'value')
            ->where('B', '~', 'text')
            ->where('C', '>', 10)
            ->where('D', '<', 20)
            ->where('E', '>=', 30)
            ->where('F', '<=', 40);

        expect((string) $query)->toBe('+A="value" +B~"text" +C>10 +D<20 +E>=30 +F<=40');
    });
});

describe('complex queries', function () {
    it('builds mount search query', function () {
        $query = SearchQuery::make()
            ->where('IsFlying')->equals(true)
            ->where('ExtraSeats')->greaterThan(0)
            ->orWhere('Name')->contains('Dragon');

        expect((string) $query)->toBe('+IsFlying=true +ExtraSeats>0 Name~"Dragon"');
    });

    it('builds item search with array field', function () {
        $query = SearchQuery::make()->whereHas('BaseParam', fn ($q) => $q
            ->where('Name')->equals('Spell Speed')
        );

        expect((string) $query)->toBe('+BaseParam[].Name="Spell Speed"');
    });

    it('builds localized search', function () {
        $query = SearchQuery::make()->where('Name')->localizedTo(Language::Japanese)->contains('ポーション');

        expect((string) $query)->toBe('+Name@ja~"ポーション"');
    });

    it('builds complex query with groups and arrays', function () {
        $query = SearchQuery::make()
            ->where('IsFlying')->equals(true)
            ->where('ExtraSeats')->greaterThan(0)
            ->orWhere('Name')->localizedTo(Language::Japanese)->contains('ドラゴン')
            ->whereGroup(fn ($q) => $q
                ->where('Level')->equals(80)
                ->where('Level')->equals(90)
            );

        expect((string) $query)->toBe('+IsFlying=true +ExtraSeats>0 Name@ja~"ドラゴン" +(+Level=80 +Level=90)');
    });

});

describe('error handling', function () {
    it('throws on unknown operator', function () {
        SearchQuery::make()->where('Field', '!=', 'value');
    })->throws(InvalidArgumentException::class, 'Unknown operator: !=');
});

describe('ArrayGroupBuilder variants', function () {
    it('whereHas with whereNot inside', function () {
        $query = SearchQuery::make()->whereHas('Items', fn ($q) => $q
            ->whereNot('Category')->equals('Weapon')
        );

        expect((string) $query)->toBe('-Items[].Category="Weapon"');
    });

    it('whereHas with orWhere inside', function () {
        $query = SearchQuery::make()->whereHas('Items', fn ($q) => $q
            ->orWhere('Name')->contains('Rare')
        );

        expect((string) $query)->toBe('Items[].Name~"Rare"');
    });

    it('whereHasNot with whereNot inside uses - prefix', function () {
        $query = SearchQuery::make()->whereHasNot('Items', fn ($q) => $q
            ->whereNot('Category')->equals('Weapon')
        );

        expect((string) $query)->toBe('-Items[].Category="Weapon"');
    });

    it('orWhereHas with multiple conditions', function () {
        $query = SearchQuery::make()->orWhereHas('Stats', fn ($q) => $q
            ->where('Name')->equals('Strength')
            ->whereNot('Value')->lessThan(10)
        );

        expect((string) $query)->toBe('Stats[].Name="Strength" -Stats[].Value<10');
    });
});

describe('ArrayGroupBuilder shortcuts', function () {
    it('where with value shortcut', function () {
        $query = SearchQuery::make()->whereHas('Items', fn ($q) => $q
            ->where('Name', 'Sword')
        );

        expect((string) $query)->toBe('+Items[].Name="Sword"');
    });

    it('where with operator shortcut', function () {
        $query = SearchQuery::make()->whereHas('Items', fn ($q) => $q
            ->where('Level', '>=', 50)
        );

        expect((string) $query)->toBe('+Items[].Level>=50');
    });

    it('whereNot with value shortcut', function () {
        $query = SearchQuery::make()->whereHas('Items', fn ($q) => $q
            ->whereNot('Category', 'Weapon')
        );

        expect((string) $query)->toBe('-Items[].Category="Weapon"');
    });

    it('orWhere with operator shortcut', function () {
        $query = SearchQuery::make()->whereHas('Items', fn ($q) => $q
            ->orWhere('Name', '~', 'Rare')
        );

        expect((string) $query)->toBe('Items[].Name~"Rare"');
    });

    it('throws on unknown operator in array builder', function () {
        SearchQuery::make()->whereHas('Items', fn ($q) => $q
            ->where('Name', '!=', 'Test')
        );
    })->throws(InvalidArgumentException::class, 'Unknown operator: !=');
});

describe('orWhere for OR logic', function () {
    it('orWhere inside whereGroup for OR logic', function () {
        $query = SearchQuery::make()
            ->where('Level')->greaterOrEqual(1)
            ->whereGroup(fn ($g) => $g
                ->orWhere('Name')->contains('Potion')
                ->orWhere('Name')->contains('Ether')
            );

        expect((string) $query)->toBe('+Level>=1 +(Name~"Potion" Name~"Ether")');
    });
});

describe('LocalizedWhereBuilder terminators', function () {
    it('supports greaterThan on localized field', function () {
        $query = SearchQuery::make()->where('Value')->localizedTo(Language::Japanese)->greaterThan(100);

        expect((string) $query)->toBe('+Value@ja>100');
    });

    it('supports lessThan on localized field', function () {
        $query = SearchQuery::make()->where('Value')->localizedTo(Language::German)->lessThan(50);

        expect((string) $query)->toBe('+Value@de<50');
    });

    it('supports greaterOrEqual on localized field', function () {
        $query = SearchQuery::make()->where('Level')->localizedTo(Language::French)->greaterOrEqual(90);

        expect((string) $query)->toBe('+Level@fr>=90');
    });

    it('supports lessOrEqual on localized field', function () {
        $query = SearchQuery::make()->where('Level')->localizedTo(Language::English)->lessOrEqual(10);

        expect((string) $query)->toBe('+Level@en<=10');
    });

    it('supports equals with boolean on localized field', function () {
        $query = SearchQuery::make()->where('IsActive')->localizedTo(Language::Japanese)->equals(true);

        expect((string) $query)->toBe('+IsActive@ja=true');
    });
});

<?php

declare(strict_types=1);

namespace XivApi\Enums;

/**
 * Languages supported by XIVAPI.
 */
enum Language: string
{
    case Japanese = 'ja';
    case English = 'en';
    case German = 'de';
    case French = 'fr';
}

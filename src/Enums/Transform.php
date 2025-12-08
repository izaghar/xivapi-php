<?php

declare(strict_types=1);

namespace XivApi\Enums;

/**
 * Field transformations for the @as() decorator.
 */
enum Transform: string
{
    /**
     * Prevents processing of relationships and icons.
     */
    case Raw = 'raw';

    /**
     * Formats string values into HTML fragments.
     */
    case Html = 'html';
}

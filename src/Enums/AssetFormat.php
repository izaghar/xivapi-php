<?php

declare(strict_types=1);

namespace XivApi\Enums;

/**
 * Supported asset output formats.
 */
enum AssetFormat: string
{
    case Jpg = 'jpg';
    case Png = 'png';
    case Webp = 'webp';
}

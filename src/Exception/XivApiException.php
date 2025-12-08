<?php

declare(strict_types=1);

namespace XivApi\Exception;

use Exception;

/**
 * Exception thrown when the XIVAPI returns an error response.
 */
class XivApiException extends Exception
{
    public function __construct(
        string $message,
        public readonly int $statusCode = 0,
    ) {
        parent::__construct($message, $statusCode);
    }

    /**
     * Create an exception from an API error response.
     */
    public static function fromResponse(int $statusCode, string $message): self
    {
        return new self($message, $statusCode);
    }
}

<?php

declare(strict_types=1);

namespace Http\Message\Tests\Fixtures;

/**
 * Allows tests to simulate a missing zlib extension.
 *
 * @see tests/functions.php
 */
final class ZlibExtension
{
    /**
     * @var bool
     */
    private static $disabled = false;

    public static function disable(): void
    {
        self::$disabled = true;
    }

    public static function reset(): void
    {
        self::$disabled = false;
    }

    public static function isLoaded(string $extension): bool
    {
        if (self::$disabled && 'zlib' === strtolower($extension)) {
            return false;
        }

        return \extension_loaded($extension);
    }
}

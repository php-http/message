<?php

declare(strict_types=1);

namespace Http\Message\Encoding;

use Http\Message\Tests\Fixtures\ZlibExtension;

/*
 * Shadows the global function for the Http\Message\Encoding namespace so tests can simulate a missing zlib extension.
 * Must be loaded before any encoding stream is constructed, hence it is registered as an autoload-dev "files" entry.
 */
function extension_loaded(string $extension): bool
{
    return ZlibExtension::isLoaded($extension);
}

<?php

declare(strict_types=1);

namespace Medas\Cookies;

use Medas\Core\Attributes\Service;

#[Service]
readonly class CookieReader
{
    // Returns the cookie with the given name from the current request, or null if it does not exist
    public function read(string $name): Cookie|null
    {
        if (!array_key_exists($name, $_COOKIE)) {
            return null;
        }

        // The browser only sends name and value; metadata (expiry, path, etc.) is not transmitted
        return Cookie::session(
            name: $name,
            value: $_COOKIE[$name],
        );
    }

    public function has(string $name): bool
    {
        return array_key_exists($name, $_COOKIE);
    }
}

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
        if (!isset($_COOKIE[$name])) {
            return null;
        }

        // The browser only sends name and value; metadata (expiry, path, etc.) is not transmitted
        return Cookie::session(
            name: $name,
            value: $_COOKIE[$name],
        );
    }

    public function readValue(string $name): string|null
    {
        return $this->read($name)?->value;
    }

    public function has(string $name): bool
    {
        return isset($_COOKIE[$name]);
    }
}

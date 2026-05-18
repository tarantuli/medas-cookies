<?php

declare(strict_types=1);

namespace Medas\Cookies;

use Medas\Core\Attributes\Service;
use Medas\HttpRequestHandler\RequestFactory;

#[Service]
readonly class CookieReader
{
    public function __construct(
        private RequestFactory $requestFactory,
    )
    {
    }

    // Returns the cookie with the given name from the current request, or null if it does not exist
    public function read(string $name): Cookie|null
    {
        $cookieData = $this->requestFactory->get()->cookieData;

        if (!isset($cookieData[$name])) {
            return null;
        }

        // The browser only sends name and value; metadata (expiry, path, etc.) is not transmitted
        return Cookie::session(
            name: $name,
            value: $cookieData[$name],
        );
    }

    public function readValue(string $name): string|null
    {
        return $this->read($name)?->value;
    }

    public function has(string $name): bool
    {
        return isset($cookieData[$name]);
    }
}

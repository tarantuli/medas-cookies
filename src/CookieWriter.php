<?php

declare(strict_types=1);

namespace Medas\Cookies;

use Medas\Core\Attributes\Service;

#[Service]
readonly class CookieWriter
{
    public function write(Cookie $cookie): void
    {
        if (headers_sent()) {
            throw new Exceptions\HeadersAlreadySent();
        }

        $parts = [rawurlencode($cookie->name) . '=' . rawurlencode($cookie->value)];

        if ($cookie->expiresAt > 0) {
            $parts[] = 'Expires=' . gmdate('D, d M Y H:i:s \G\M\T', $cookie->expiresAt);
            $parts[] = 'Max-Age=' . max(0, $cookie->expiresAt - time());
        }

        if ($cookie->path !== '') {
            $parts[] = 'Path=' . $cookie->path;
        }

        if ($cookie->domain !== '') {
            $parts[] = 'Domain=' . $cookie->domain;
        }

        if ($cookie->secure) {
            $parts[] = 'Secure';
        }

        if ($cookie->httpOnly) {
            $parts[] = 'HttpOnly';
        }

        $parts[] = 'SameSite=' . $cookie->sameSite->value;

        // false = allow multiple Set-Cookie headers on the same response
        header('Set-Cookie: ' . implode('; ', $parts), false);
    }
}

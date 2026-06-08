<?php

declare(strict_types=1);

namespace Medas\Cookies;

use Medas\Core\Attributes\Service;
use Medas\HttpRequestHandler\{
    ResponseDispatcher\ExceptionJob,
    ResponseDispatcher\Job,
    ResponseModifiers\ResponseModifier
};

#[Service]
readonly class CookieWriter implements ResponseModifier
{
    public function __construct(
        private Jar $jar = new Jar(),
    )
    {
    }

    public function write(Cookie $cookie): void
    {
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
        $this->jar->cookies[] = implode('; ', $parts);
    }

    public function handle(Job|ExceptionJob $job): void
    {
        foreach ($this->jar->cookies as $cookie) {
            $job->addHeader('Set-Cookie', $cookie);
        }

        $this->jar->cookies = [];
    }

    public function priority(): int
    {
        return -100;
    }
}

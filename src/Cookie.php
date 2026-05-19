<?php

declare(strict_types=1);

namespace Medas\Cookies;

readonly class Cookie
{
    // Creates a session cookie that expires when the browser closes
    public static function session(
        string   $name,
        string   $value,
        string   $path = '/',
        string   $domain = '',
        bool     $secure = true,
        bool     $httpOnly = true,
        SameSite $sameSite = SameSite::Lax,
    ): self
    {
        return new self(
            name: $name,
            value: $value,
            expiresAt: 0,
            path: $path,
            domain: $domain,
            secure: $secure,
            httpOnly: $httpOnly,
            sameSite: $sameSite,
        );
    }

    // Creates a persistent cookie that expires after the given number of seconds
    public static function persistent(
        string   $name,
        string   $value,
        int      $ttlSeconds,
        string   $path = '/',
        string   $domain = '',
        bool     $secure = true,
        bool     $httpOnly = true,
        SameSite $sameSite = SameSite::Lax,
    ): self
    {
        return new self(
            name: $name,
            value: $value,
            expiresAt: time() + $ttlSeconds,
            path: $path,
            domain: $domain,
            secure: $secure,
            httpOnly: $httpOnly,
            sameSite: $sameSite,
        );
    }

    // Creates a cookie with a past expiry date, instructing the browser to delete it
    public static function delete(
        string $name,
        string $path = '/',
        string $domain = '',
        bool   $secure = false,
    ): self
    {
        return new self(
            name: $name,
            value: '',
            expiresAt: 1,
            path: $path,
            domain: $domain,
            secure: $secure,
            httpOnly: false,
            sameSite: SameSite::Lax,
        );
    }

    public function __construct(
        public string   $name,
        public string   $value,

        // Unix timestamp; 0 means the cookie expires at the end of the browser session
        public int      $expiresAt,
        public string   $path,
        public string   $domain,
        public bool     $secure,
        public bool     $httpOnly,
        public SameSite $sameSite,
    )
    {
        if ($this->sameSite === SameSite::None && !$this->secure) {
            throw new Exceptions\CookieWithSameSiteNoneRequiresSecureTrue($this->name);
        }
    }
}

# medas-cookies

Part of the [Medas framework](https://github.com/tarantuli/medas-core).

## Description

Provides reading and writing of HTTP cookies, integrated with `medas-http-request-handler`.

`Cookie` is an immutable value object with three named constructors:

| Method                 | `expiresAt`            | Use case                                   |
|------------------------|------------------------|--------------------------------------------|
| `Cookie::session()`    | `0`                    | Expires when the browser closes            |
| `Cookie::persistent()` | `time() + $ttlSeconds` | Survives browser restarts                  |
| `Cookie::delete()`     | `1` (past)             | Instructs the browser to remove the cookie |

`CookieWriter` implements `ResponseModifier` and queues `Set-Cookie` headers on the outgoing response. It is registered at priority − 100, so cookies are written before the response is dispatched. Both `Expires` and `Max-Age` are written for persistent cookies for maximum browser compatibility.

`CookieReader` reads incoming cookie values from the current request via `RequestFactory`. Because browsers only transmit name and value (no metadata), `read()` always returns a session-style `Cookie`.

`CookieValueResolver` is a `ParameterResolver` registered at priority −185 that injects cookie values into service constructors and promoted properties annotated with `#[CookieValue('name')]`.

`SameSite::None` requires `$secure = true`; constructing a `Cookie` with that combination throws `CookieWithSameSiteNoneRequiresSecureTrue`.

## Usage

### Package developer context

Register the package — it self-registers `CookieValueResolver` as a parameter resolver:

```php
use Medas\Cookies\CookiesPackage;

CookiesPackage::instance()->initialize($config);
```

**Writing a cookie in a request handler:**

```php
use Medas\Cookies\{Cookie, CookieWriter, SameSite};
use Medas\Core\Attributes\Service;

#[Service]
readonly class LoginHandler
{
    public function __construct(
        private CookieWriter $cookieWriter,
    ) {}

    public function handle(string $userId): void
    {
        // Persistent cookie lasting 30 days, Secure + HttpOnly + Lax by default
        $this->cookieWriter->write(Cookie::persistent(
            name: 'session_id',
            value: $userId,
            ttlSeconds: 30 * 24 * 3600,
        ));

        // Session cookie (cleared when browser closes)
        $this->cookieWriter->write(Cookie::session(
            name: 'flash',
            value: 'Login successful',
        ));

        // Cross-site cookie (e.g., for embedded widgets); requires secure: true
        $this->cookieWriter->write(Cookie::persistent(
            name: 'tracker',
            value: 'abc123',
            ttlSeconds: 3600,
            sameSite: SameSite::None,
            secure: true,
        ));
    }
}
```

**Deleting a cookie:**

```php
$this->cookieWriter->write(Cookie::delete(name: 'session_id'));
```

The path and domain must match those used when the cookie was set, otherwise the browser will not remove it.

**Reading a cookie in a request handler:**

```php
use Medas\Cookies\CookieReader;
use Medas\Core\Attributes\Service;

#[Service]
readonly class SessionMiddleware
{
    public function __construct(
        private CookieReader $cookieReader,
    ) {}

    public function getSessionId(): string|null
    {
        // Returns the string value or null if the cookie is absent
        return $this->cookieReader->readValue('session_id');
    }

    public function hasSession(): bool
    {
        return $this->cookieReader->has('session_id');
    }

    public function getFullCookie(): Cookie|null
    {
        // Returns a Cookie value object (always session-style, since browsers
        // do not transmit expiry or path metadata)
        return $this->cookieReader->read('session_id');
    }
}
```

**Injecting a cookie value directly into a service:**

```php
use Medas\Cookies\Attributes\CookieValue;
use Medas\Core\Attributes\Service;

#[Service]
readonly class CurrentUser
{
    public function __construct(
        // Resolved automatically by CookieValueResolver; null if the cookie is absent
        #[CookieValue('session_id')]
        public string|null $sessionId,
    ) {}
}
```

`#[CookieValue]` can be placed on constructor parameters or promoted properties. If the named cookie is not present, the resolver does not bind the parameter, leaving it at its default value (or `null` if the type permits).

**Constructing a `Cookie` directly:**

```php
use Medas\Cookies\{Cookie, SameSite};

$cookie = new Cookie(
    name: 'prefs',
    value: 'dark-mode',
    expiresAt: time() + 86400,
    path: '/',
    domain: 'example.com',
    secure: true,
    httpOnly: false,
    sameSite: SameSite::Strict,
);
```

### Backend user context

Cookies are written transparently as part of the HTTP response — no manual `Set-Cookie` header management is needed. The relevant points for backend configuration are:

**Secure flag** — always `true` by default on `Cookie::session()` and `Cookie::persistent()`. Set `secure: false` only on non-HTTPS environments (e.g., local development).

**`SameSite` values:**

| Value           | When the cookie is sent                                 |
|-----------------|---------------------------------------------------------|
| `Strict`        | Same-site requests only                                 |
| `Lax` (default) | Same-site requests and top-level cross-site navigations |
| `None`          | All requests (requires `secure: true`)                  |

**Cookie lifetime:**

```php
// 1 hour
Cookie::persistent('token', $value, ttlSeconds: 3600);

// 7 days
Cookie::persistent('remember_me', $value, ttlSeconds: 7 * 24 * 3600);

// Session (browser close)
Cookie::session('flash', $value);
```

**Deleting a cookie from a specific path or domain:**

```php
Cookie::delete(name: 'session_id', path: '/app', domain: 'example.com');
```

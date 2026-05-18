<?php

declare(strict_types=1);

namespace Medas\Cookies;

enum SameSite: string
{
    // Browser only sends the cookie for same-site requests
    case Strict = 'Strict';

    // Browser sends the cookie for same-site requests and top-level cross-site navigations
    case Lax = 'Lax';

    // Browser sends the cookie for all requests; requires Secure to be true
    case None = 'None';
}

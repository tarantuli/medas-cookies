<?php

declare(strict_types=1);

namespace Medas\Cookies\Exceptions;

use Medas\Core\Exceptions\BaseException;

class CookieWithSameSiteNoneRequiresSecureTrue extends BaseException
{
    public function __construct(string $name)
    {
        parent::__construct($name);
    }

    public function pattern(): string
    {
        return 'Cookie with name: %s and SameSite=None requires Secure to be true';
    }
}

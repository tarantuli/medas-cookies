<?php

declare(strict_types=1);

namespace Medas\Cookies\Exceptions;

use Medas\Core\Exceptions\BaseException;

class NoCookieValueFound extends BaseException
{
    public function __construct(string $name)
    {
        parent::__construct($name);
    }

    public function pattern(): string
    {
        return 'no cookie value found for name: %s';
    }
}

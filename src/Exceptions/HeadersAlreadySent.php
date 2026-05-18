<?php

declare(strict_types=1);

namespace Medas\Cookies\Exceptions;

use Medas\Core\Exceptions\BaseException;

class HeadersAlreadySent extends BaseException
{
    public function pattern(): string
    {
        return 'Cannot write cookie: headers have already been sent';
    }
}

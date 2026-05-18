<?php

declare(strict_types=1);

namespace Medas\Cookies\Attributes;

/*
 * Allow both to allow the attribute to be assigned to parameters in constructors with property promotion.
 */
#[\Attribute(\Attribute::TARGET_PARAMETER | \Attribute::TARGET_PROPERTY)]
readonly class CookieValue
{
    public function __construct(
        public string $name,
    )
    {
    }
}

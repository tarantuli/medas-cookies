<?php

declare(strict_types=1);

namespace Medas\Cookies;

use Medas\Core\{Attributes\Service, Interfaces\ParameterResolver, ParameterResolverResult};

#[Service]
readonly class CookieValueResolver implements ParameterResolver
{
    public function __construct(
        private CookieReader $cookieReader,
    )
    {
    }

    public function priority(): int
    {
        return -185;
    }

    public function handle(\ReflectionParameter|\ReflectionProperty $parameter): ParameterResolverResult
    {
        if (!$attribute = attribute(Attributes\CookieValue::class, $parameter)) {
            return new ParameterResolverResult(false);
        }

        if (!$this->cookieReader->has($attribute->name)) {
            return new ParameterResolverResult(false);
        }

        return new ParameterResolverResult(true, $this->cookieReader->readValue($attribute->name));
    }
}

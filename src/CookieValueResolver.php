<?php

declare(strict_types=1);

namespace Medas\Cookies;

use Medas\Core\{Interfaces\ParameterResolver, ParameterResolverResult};

readonly class CookieValueResolver implements ParameterResolver
{
    public function priority(): int
    {
        return -185;
    }

    public function handle(\ReflectionParameter|\ReflectionProperty $parameter): ParameterResolverResult
    {
        $reader = service(CookieReader::class);

        if (!$attribute = attribute(Attributes\CookieValue::class, $parameter)) {
            return new ParameterResolverResult(false);
        }

        if (!$reader->has($attribute->name)) {
            return new ParameterResolverResult(false);
        }

        return new ParameterResolverResult(true, $reader->readValue($attribute->name));
    }
}

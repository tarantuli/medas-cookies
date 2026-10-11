<?php

declare(strict_types=1);

namespace Medas\Cookies;

use Medas\Core\{
    HandlesByCompiling,
    Interfaces\CompilableParameterResolver,
    ParameterResolverResult
};

readonly class CookieValueResolver implements CompilableParameterResolver
{
    use HandlesByCompiling;

    public function priority(): int
    {
        return -185;
    }

    public function compile(\ReflectionParameter|\ReflectionProperty $parameter): Attributes\CookieValue|null
    {
        return attribute(Attributes\CookieValue::class, $parameter);
    }

    /**
     * @param Attributes\CookieValue $compiled
     */
    public function resolveCompiled(object $compiled): ParameterResolverResult
    {
        $reader = service(CookieReader::class);

        if (!$reader->has($compiled->name)) {
            return new ParameterResolverResult(false);
        }

        return new ParameterResolverResult(true, $reader->readValue($compiled->name));
    }
}

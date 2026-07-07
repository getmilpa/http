<?php

/**
 * This file is part of Milpa HTTP — the web tier of the Milpa PHP framework.
 *
 * (c) TeamX Agency — https://teamx.agency <hola@teamx.agency>
 *
 * @license Apache-2.0
 *
 * @link    https://github.com/getmilpa/http
 */

declare(strict_types=1);

namespace Milpa\Http\Exceptions;

/**
 * Thrown by {@see \Milpa\Http\Routing\UrlGeneratorInterface::generate()} for a route name
 * that was never registered. A match miss is NOT this exception — it is
 * {@see \Milpa\Http\Routing\RouteResult::notFound()}; this is generation-only (a bug in the caller).
 */
final class RouteNotFoundException extends \RuntimeException implements RoutingExceptionInterface
{
    /** Build the exception for an unknown route name. */
    public static function forName(string $name): self
    {
        return new self(\sprintf('No route is registered under the name "%s".', $name));
    }
}

<?php

/**
 * This file is part of Milpa HTTP — the web tier of the Milpa PHP framework.
 *
 * (c) Rodrigo Vicente - TeamX Agency — https://teamx.agency <hola@teamx.agency>
 *
 * @license Apache-2.0
 *
 * @link    https://github.com/getmilpa/http
 */

declare(strict_types=1);

namespace Milpa\Http\Exceptions;

/**
 * Thrown by {@see \Milpa\Http\Routing\UrlGeneratorInterface::generate()} when a route's
 * required path parameters were not all supplied.
 */
final class MissingRouteParametersException extends \InvalidArgumentException implements RoutingExceptionInterface
{
    /**
     * Build the exception for a route missing one or more required parameters.
     *
     * @param list<string> $missing
     */
    public static function forRoute(string $name, array $missing): self
    {
        return new self(\sprintf('Route "%s" is missing required parameter(s): %s.', $name, \implode(', ', $missing)));
    }
}

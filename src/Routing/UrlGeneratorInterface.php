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

namespace Milpa\Http\Routing;

use Milpa\Http\Exceptions\MissingRouteParametersException;
use Milpa\Http\Exceptions\RouteNotFoundException;

/**
 * Reverse routing: build a URL for a named route from its parameters. Kept as its own
 * interface (not bolted onto {@see RouterInterface}) to hold one responsibility per type —
 * matching is request→route, generation is name→URI.
 */
interface UrlGeneratorInterface
{
    /**
     * Build the URL for a named route.
     *
     * @param array<string, string|int> $parameters    path-param values (surplus values become query string)
     * @param UrlReferenceType          $referenceType how the reference is rendered
     *
     * @throws RouteNotFoundException          when no route is registered under $name
     * @throws MissingRouteParametersException when a required path parameter is not supplied
     */
    public function generate(string $name, array $parameters = [], UrlReferenceType $referenceType = UrlReferenceType::ABSOLUTE_PATH): string;
}

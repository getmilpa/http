<?php

/**
 * This file is part of Milpa HTTP — the web tier of the Milpa PHP framework.
 *
 * (c) TeamX Agency — https://teamx.agency <hola@teamx.agency>
 *
 * @license Apache-2.0
 * @link    https://github.com/getmilpa/http
 */

declare(strict_types=1);

namespace Milpa\Http\Routing;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Matches a PSR-7 request against the route table.
 *
 * A pure function of the request: it never throws for a miss and never constructs a
 * response — a 404 or 405 is the expected {@see RouteResult}, not an exception. How routes
 * are registered (attribute scanning, config, code) is deliberately an implementation
 * concern, kept out of this contract.
 */
interface RouterInterface
{
    /**
     * Resolve the request to a typed result — always MATCHED, NOT_FOUND or
     * METHOD_NOT_ALLOWED, never null.
     */
    public function match(ServerRequestInterface $request): RouteResult;
}

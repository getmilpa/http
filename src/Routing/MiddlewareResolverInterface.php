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

namespace Milpa\Http\Routing;

use Psr\Http\Server\MiddlewareInterface;

/**
 * Resolves a route's per-route middleware references — the `class-string`s carried on
 * {@see Route}'s `$middleware` — into live PSR-15 middleware. A dispatch middleware composes
 * these in front of the resolved handler; the concrete resolver (pulling instances from the
 * DI container) lives in the host kernel. The middleware counterpart of
 * {@see HandlerResolverInterface}.
 */
interface MiddlewareResolverInterface
{
    /**
     * Resolve a per-route middleware reference into an executable PSR-15 middleware.
     *
     * @param class-string $middleware
     */
    public function resolve(string $middleware): MiddlewareInterface;
}

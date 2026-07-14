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

use Psr\Http\Server\RequestHandlerInterface;

/**
 * The seam between the abstract route table and concrete PSR-15 execution: it turns a
 * matched route's framework-agnostic {@see HandlerReference} into a live
 * `Psr\Http\Server\RequestHandlerInterface` — pulling the controller from the container and
 * adapting a method-as-handler. A dispatch middleware depends on this, not on the container.
 */
interface HandlerResolverInterface
{
    /** Resolve a handler reference into an executable PSR-15 request handler. */
    public function resolve(HandlerReference $reference): RequestHandlerInterface;
}

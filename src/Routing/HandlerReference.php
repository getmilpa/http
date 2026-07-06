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

/**
 * An immutable, framework-agnostic pointer to the code a route runs: a controller class
 * plus the method that handles the request (defaulting to `__invoke` for a single-action
 * handler class). Serializable, so route tables can be compiled and cached; a
 * {@see HandlerResolverInterface} turns it into a live PSR-15 request handler.
 */
final readonly class HandlerReference implements \Stringable
{
    /**
     * @param class-string $controller the handler class
     * @param string       $method     the method that handles the request
     */
    public function __construct(
        public string $controller,
        public string $method = '__invoke',
    ) {
    }

    /**
     * Reference a single-action handler class through its `__invoke` method.
     *
     * @param class-string $controller
     */
    public static function action(string $controller): self
    {
        return new self($controller);
    }

    /**
     * Reference a specific method on a controller class.
     *
     * @param class-string $controller
     */
    public static function method(string $controller, string $method): self
    {
        return new self($controller, $method);
    }

    /** Render the reference as `Controller::method`. */
    public function __toString(): string
    {
        return $this->controller . '::' . $this->method;
    }
}

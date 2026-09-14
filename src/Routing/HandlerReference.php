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

    /**
     * The reference behind `[Controller::class, 'method']` — the way a route names its handler.
     *
     * Anything else is refused here, by shape and naming what was received: a route whose handler cannot
     * be resolved would otherwise surface at dispatch, as a 500 to whoever asked, instead of at
     * declaration, to whoever wrote it. Only the pair is accepted: a bare `Controller::class` for a
     * single-action handler arrives with its first consumer in the family (greenhouse decisions/0386).
     *
     * @param array<mixed> $handler
     */
    public static function of(array $handler): self
    {
        if (
            \count($handler) === 2
            && array_is_list($handler)
            && \is_string($handler[0]) && $handler[0] !== ''
            && \is_string($handler[1]) && $handler[1] !== ''
        ) {
            return new self($handler[0], $handler[1]);
        }

        throw new \InvalidArgumentException(
            'A handler is named as [Controller::class, \'method\']; received '
            . json_encode($handler, \JSON_INVALID_UTF8_SUBSTITUTE | \JSON_PARTIAL_OUTPUT_ON_ERROR) . '.',
        );
    }

    /** Render the reference as `Controller::method`. */
    public function __toString(): string
    {
        return $this->controller . '::' . $this->method;
    }
}

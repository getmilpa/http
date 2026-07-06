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

use Milpa\Http\HttpMethod;

/**
 * The one canonical route: the same immutable type you declare with `#[Route(...)]` on a
 * controller method AND the value a matcher returns inside a {@see RouteResult}. It holds
 * only static route facts — path, verbs, name, host, priority, defaults and per-route
 * middleware; per-request path arguments live on the RouteResult.
 *
 * The `handler` is null only between declaration (an attribute cannot name its own method
 * as a constant expression) and binding: the kernel calls {@see self::withHandler()} once
 * reflection supplies the controller and method. Assert it with {@see self::isBound()}.
 */
#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
final readonly class Route
{
    /** @var non-empty-list<HttpMethod> */
    public array $methods;

    /**
     * @param HttpMethod|non-empty-list<HttpMethod> $methods    one verb, or a list of verbs
     * @param array<string, string>                 $defaults   default values for optional path params
     * @param list<class-string>                    $middleware per-route PSR-15 middleware (kernel resolves via PSR-11)
     */
    public function __construct(
        public string $path,
        HttpMethod|array $methods = HttpMethod::GET,
        public ?string $name = null,
        public ?string $host = null,
        public int $priority = 0,
        public array $defaults = [],
        public array $middleware = [],
        public ?HandlerReference $handler = null,
    ) {
        $this->methods = \is_array($methods) ? $methods : [$methods];
    }

    /** Bind the handler discovered by attribute reflection, returning a new instance. */
    public function withHandler(HandlerReference $handler): self
    {
        return new self($this->path, $this->methods, $this->name, $this->host, $this->priority, $this->defaults, $this->middleware, $handler);
    }

    /** Return a copy carrying the given route name. */
    public function withName(string $name): self
    {
        return new self($this->path, $this->methods, $name, $this->host, $this->priority, $this->defaults, $this->middleware, $this->handler);
    }

    /** Whether this route accepts the given HTTP method. */
    public function allows(HttpMethod $method): bool
    {
        return \in_array($method, $this->methods, true);
    }

    /** Whether the handler has been bound (never null once the kernel has processed the route). */
    public function isBound(): bool
    {
        return $this->handler !== null;
    }
}

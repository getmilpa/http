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
     * `$host`, `$priority` and `$defaults` used to sit here and were removed: the matcher never read one
     * of them, so they were three promises this package did not keep — and a declaration a reader can set
     * and nothing obeys is worse than an absent feature, because it looks like a feature (greenhouse
     * decisions/0213 row 5, decisions/0215). Whoever wants host matching or an explicit priority proposes
     * them WITH the matcher that honours them.
     *
     * @param HttpMethod|non-empty-list<HttpMethod> $methods    one verb, or a list of verbs
     * @param list<class-string>                    $middleware per-route PSR-15 middleware (kernel resolves via PSR-11)
     */
    public function __construct(
        public string $path,
        HttpMethod|array $methods = HttpMethod::GET,
        public ?string $name = null,
        public array $middleware = [],
        public ?HandlerReference $handler = null,
    ) {
        $this->methods = \is_array($methods) ? $methods : [$methods];
    }

    /** Bind the handler discovered by attribute reflection, returning a new instance. */
    public function withHandler(HandlerReference $handler): self
    {
        return new self($this->path, $this->methods, $this->name, $this->middleware, $handler);
    }

    /** Return a copy carrying the given route name. */
    public function withName(string $name): self
    {
        return new self($this->path, $this->methods, $name, $this->middleware, $this->handler);
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

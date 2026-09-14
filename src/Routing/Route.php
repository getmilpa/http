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

    /**
     * A `GET` route, its handler named the way a controller is named: `[Controller::class, 'method']`.
     *
     * The constructor stays the canon; this produces the same value with the verb said once, as the
     * method name, and the reference derived from the pair ({@see HandlerReference::of()}, greenhouse
     * decisions/0386). Only the verbs with a consumer in the family exist here — `put`/`patch`/`delete`
     * arrive with their first caller.
     *
     * @param array<mixed> $handler
     */
    public static function get(string $path, array $handler, ?string $name = null): self
    {
        return new self($path, HttpMethod::GET, $name, [], HandlerReference::of($handler));
    }

    /**
     * A `POST` route — the handler is named as in {@see self::get()}.
     *
     * @param array<mixed> $handler
     */
    public static function post(string $path, array $handler, ?string $name = null): self
    {
        return new self($path, HttpMethod::POST, $name, [], HandlerReference::of($handler));
    }

    /**
     * The same routes, each behind one door: the given middleware is prepended — outermost, the position
     * the constructor's list gives it — to whatever each route already carries, so a provider declares
     * its gate once for the group instead of once per route. Keys are not preserved: the argument is a list.
     *
     * @param list<class-string> $middleware
     *
     * @return list<self>
     */
    public static function behind(array $middleware, self ...$routes): array
    {
        return array_map(
            static fn (self $route): self => $route->withMiddleware([...$middleware, ...$route->middleware]),
            $routes,
        );
    }

    /**
     * Return a copy carrying the given per-route middleware, outermost first.
     *
     * @param list<class-string> $middleware
     */
    public function withMiddleware(array $middleware): self
    {
        return new self($this->path, $this->methods, $this->name, $middleware, $this->handler);
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

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

use Milpa\Http\HttpMethod;

/**
 * The immutable, never-null outcome of {@see RouterInterface::match()} — and the only value
 * routing injects into the request. A success carries the matched {@see Route} plus the
 * extracted path arguments; a method failure carries the verbs the path does allow (for the
 * `Allow` header); a miss carries nothing. Built only through the three named constructors,
 * so illegal states (a match with no route, a 405 with no verbs) are unrepresentable.
 */
final readonly class RouteResult
{
    /** The PSR-7 request-attribute key a routing middleware stores this result under. */
    public const string ATTRIBUTE = self::class;

    /**
     * @param array<string, string> $parameters     extracted path params (MATCHED only)
     * @param list<HttpMethod>      $allowedMethods verbs the path allows (METHOD_NOT_ALLOWED only)
     */
    private function __construct(
        public MatchStatus $status,
        public ?Route $route,
        public array $parameters,
        public array $allowedMethods,
    ) {
    }

    /**
     * A successful match: the route and its extracted path arguments.
     *
     * @param array<string, string> $parameters
     */
    public static function matched(Route $route, array $parameters = []): self
    {
        return new self(MatchStatus::MATCHED, $route, $parameters, []);
    }

    /** No route matched the path (→ 404). */
    public static function notFound(): self
    {
        return new self(MatchStatus::NOT_FOUND, null, [], []);
    }

    /**
     * The path matched but the method did not (→ 405); carries the allowed verbs.
     *
     * @param non-empty-list<HttpMethod> $allowedMethods
     */
    public static function methodNotAllowed(array $allowedMethods): self
    {
        return new self(MatchStatus::METHOD_NOT_ALLOWED, null, [], $allowedMethods);
    }

    /** Whether a route was matched. */
    public function isMatched(): bool
    {
        return $this->status === MatchStatus::MATCHED;
    }

    /** Read a single extracted path parameter, or the default when it is absent. */
    public function parameter(string $name, ?string $default = null): ?string
    {
        return $this->parameters[$name] ?? $default;
    }
}

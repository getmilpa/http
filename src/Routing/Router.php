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
use Psr\Http\Message\ServerRequestInterface;

/**
 * The family's reference {@see RouterInterface} implementation: exact path segments plus
 * single-segment `{placeholder}` parameters. Never throws, never returns null — a
 * {@see RouteResult} always carries the outcome, exactly as the contract demands.
 *
 * A trailing slash is normalized away before matching (`/posts/` and `/posts` are the same
 * route), and the root path always matches as `/`. Route order matters for the `Allow`
 * header on a method miss: every route whose path matches the request, regardless of
 * method, contributes its verbs to the eventual {@see MatchStatus::METHOD_NOT_ALLOWED}
 * result.
 */
final class Router implements RouterInterface
{
    /** @var list<Route> */
    private readonly array $routes;

    /** @param Route ...$routes the route table, in match priority order */
    public function __construct(Route ...$routes)
    {
        $this->routes = array_values($routes);
    }

    /**
     * Resolves the request to a typed result — always MATCHED, NOT_FOUND or
     * METHOD_NOT_ALLOWED, never null.
     */
    public function match(ServerRequestInterface $request): RouteResult
    {
        $path = rtrim($request->getUri()->getPath(), '/') ?: '/';
        $method = HttpMethod::tryFrom(strtoupper($request->getMethod()));
        $allowedElsewhere = [];

        foreach ($this->routes as $route) {
            $params = $this->pathParams($route->path, $path);
            if ($params === null) {
                continue;
            }
            if ($method !== null && $route->allows($method)) {
                return RouteResult::matched($route, $params);
            }
            foreach ($route->methods as $allowed) {
                $allowedElsewhere[] = $allowed;
            }
        }

        return $allowedElsewhere === []
            ? RouteResult::notFound()
            : RouteResult::methodNotAllowed($allowedElsewhere);
    }

    /**
     * Matches a route pattern against a path segment-by-segment, extracting
     * `{placeholder}` values along the way.
     *
     * @return array<string, string>|null the extracted parameters when the path matches
     *                                    the pattern, null otherwise
     */
    private function pathParams(string $pattern, string $path): ?array
    {
        $patternParts = explode('/', trim($pattern, '/'));
        $pathParts = explode('/', trim($path, '/'));
        if (\count($patternParts) !== \count($pathParts)) {
            return null;
        }
        $params = [];
        foreach ($patternParts as $i => $part) {
            if (preg_match('/^\{(\w+)\}$/', $part, $m) === 1) {
                $params[$m[1]] = $pathParts[$i];
            } elseif ($part !== $pathParts[$i]) {
                return null;
            }
        }

        return $params;
    }
}

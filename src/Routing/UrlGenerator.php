<?php

/**
 * This file is part of Milpa HTTP — the routing and HTTP contracts of the Milpa PHP framework.
 *
 * (c) Rodrigo Vicente - TeamX Agency — https://teamx.agency <hola@teamx.agency>
 *
 * @license Apache-2.0
 *
 * @link    https://github.com/getmilpa/http
 */

declare(strict_types=1);

namespace Milpa\Http\Routing;

use Milpa\Http\Exceptions\MissingRouteParametersException;
use Milpa\Http\Exceptions\RouteNotFoundException;

/**
 * Reverse routing over the table the router already holds: a route's NAME and its parameters in, its
 * path out.
 *
 * Until this existed, `UrlGeneratorInterface` had zero implementations and every URL in the framework
 * was a string literal — so a path could be changed in its route declaration and left stale in the
 * three other places that named it, with nothing to notice (greenhouse decisions/0215, F4).
 *
 * ── WHY ONLY `ABSOLUTE_PATH` ────────────────────────────────────────────────────────────────────
 *
 * {@see UrlReferenceType} declared four kinds of reference. Three of them — an absolute URL, a
 * network path, a relative path — need a REQUEST CONTEXT: a scheme, a host, a base path, the path
 * being rendered from. **Nothing in this framework holds any of that**: measured across the routing,
 * runtime and web packages, there are zero occurrences of a base path, a `SCRIPT_NAME`, a request
 * context or an `app.url` config key, and the skeleton mounts at `/`.
 *
 * So those three cases were REMOVED rather than stubbed or thrown from. A declaration a reader can
 * set and nothing obeys is worse than an absent feature, because it looks like a feature — the same
 * rule that took `Route::$host` out of this package. Whoever needs an absolute URL proposes it WITH
 * the request context that renders it.
 *
 * ── SURPLUS PARAMETERS BECOME A QUERY STRING ────────────────────────────────────────────────────
 *
 * The interface says so, and it is the useful half: `generate('post', ['id' => 7, 'page' => 2])` on
 * `/posts/{id}` answers `/posts/7?page=2`. What it never does is guess — a MISSING path parameter is
 * refused by name, because a URL with a literal `{id}` in it is a bug that travels.
 */
final class UrlGenerator implements UrlGeneratorInterface
{
    /** @var array<string, Route> */
    private readonly array $byName;

    /**
     * Index the router's table by route name, once.
     *
     * A route without a name cannot be generated and is skipped rather than refused: naming is
     * optional in {@see Route}, and a table that cannot be indexed would make reverse routing
     * impossible for every app that has one unnamed route.
     */
    public function __construct(Router $router)
    {
        $byName = [];
        foreach ($router->routes() as $route) {
            if ($route->name !== null && $route->name !== '') {
                $byName[$route->name] ??= $route;
            }
        }

        $this->byName = $byName;
    }

    /**
     * Build the path for a named route, filling its parameters and appending the surplus as a query.
     *
     * @param array<string, string|int> $parameters
     *
     * @throws RouteNotFoundException          when no route is registered under `$name`
     * @throws MissingRouteParametersException when a path parameter has no value
     */
    public function generate(string $name, array $parameters = [], UrlReferenceType $referenceType = UrlReferenceType::ABSOLUTE_PATH): string
    {
        $route = $this->byName[$name] ?? throw RouteNotFoundException::forName($name);

        $missing = [];
        $used = [];
        $path = preg_replace_callback(
            '/\{(\w+)\}/',
            static function (array $match) use ($parameters, &$missing, &$used): string {
                $key = $match[1];
                if (!\array_key_exists($key, $parameters)) {
                    $missing[] = $key;

                    return $match[0];
                }

                $used[$key] = true;

                return rawurlencode((string) $parameters[$key]);
            },
            $route->path,
        );

        if ($missing !== []) {
            throw MissingRouteParametersException::forRoute($name, $missing);
        }

        $query = array_diff_key($parameters, $used);

        return $query === [] ? (string) $path : $path . '?' . http_build_query($query);
    }
}

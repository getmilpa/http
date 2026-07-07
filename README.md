<p align="center">
  <a href="https://github.com/getmilpa">
    <picture>
      <source media="(prefers-color-scheme: dark)" srcset="https://raw.githubusercontent.com/getmilpa/core/main/art/lockup/milpa-lockup-v-color-dark.svg">
      <img src="https://raw.githubusercontent.com/getmilpa/core/main/art/lockup/milpa-lockup-v-color-light.svg" alt="Milpa" width="300">
    </picture>
  </a>
</p>

# Milpa HTTP

> **PSR-15-native routing contracts** for the Milpa PHP framework, built on **`milpa/core`**. One immutable `Route` that is both the `#[Route]` attribute and the matched value; a router that turns a PSR-7 request into a typed `RouteResult` (matched / not-found / method-not-allowed) and never throws for a miss; and typed seams onto PSR-15 handlers and middleware.

[![CI](https://github.com/getmilpa/http/actions/workflows/ci.yml/badge.svg)](https://github.com/getmilpa/http/actions/workflows/ci.yml)
[![Packagist](https://img.shields.io/packagist/v/milpa/http.svg)](https://packagist.org/packages/milpa/http)
[![PHP](https://img.shields.io/badge/php-%E2%89%A5%208.3-777bb4.svg)](https://www.php.net/)
[![License](https://img.shields.io/badge/license-Apache--2.0-blue.svg)](LICENSE)
[![Docs](https://img.shields.io/badge/docs-API%20reference-blue.svg)](https://getmilpa.github.io/http/)

`milpa/http` carries the contracts for Milpa's web tier — routing and its integration with
PSR-15 middleware, and nothing else. It sits one layer above `milpa/core` and speaks the
PHP-FIG standards directly: PSR-7 for the request, PSR-15 for handlers and middleware. **No
kernel, no concrete router, no middleware runner** — just the typed seams everything binds to.

## Install

```bash
composer require milpa/http
```

## What it is

Milpa splits its surface into small, dependency-light contract packages. `milpa/core` holds
the framework-agnostic heart; `milpa/http` adds the **web tier** on top. It is deliberately
minimal:

- **`HttpMethod`** — a typed verb enum (`isSafe()`, `isIdempotent()`). No `'GET|POST'` strings.
- **`Route`** — one immutable value that is *also* the `#[Route]` attribute you put on a
  handler. Path, verbs, name, host, priority, defaults, and per-route middleware — the single
  source of truth used by both the declaration and the match.
- **`RouterInterface`** — `match(ServerRequestInterface): RouteResult`. A pure function: it
  never throws for a miss and never builds a response. A 404 or 405 is an expected result.
- **`RouteResult`** — the typed, never-null outcome (`MATCHED` / `NOT_FOUND` /
  `METHOD_NOT_ALLOWED`), built through named constructors so illegal states can't exist.
- **`HandlerResolverInterface`** & **`MiddlewareResolverInterface`** — the two seams onto
  PSR-15: turn a matched route's `HandlerReference` into a live `RequestHandlerInterface`, and
  its per-route middleware `class-string`s into live `MiddlewareInterface`s.
- **`UrlGeneratorInterface`** — reverse routing (name → URL) with a typed `UrlReferenceType`.

**Be honest about scope:** this package ships the **contracts only**. It does not match
requests, dispatch middleware, or boot a server by itself — it defines the seams a concrete
web runtime implements. Templating and lifecycle events are *not* here: routing is a PSR-15
middleware pipeline, and the view layer is a separate tier.

## The shape

Declare a route with the `#[Route]` attribute — it *is* a `Route`, repeatable on one method:

```php
use Milpa\Http\HttpMethod;
use Milpa\Http\Routing\Route;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class ShowUser
{
    #[Route('/users/{id}', HttpMethod::GET, name: 'users.show')]
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        // ...
    }
}
```

Match a PSR-7 request and branch on the typed status — no nulls, no exceptions for a miss:

```php
use Milpa\Http\Routing\MatchStatus;
use Milpa\Http\Routing\RouteResult;
use Milpa\Http\Routing\RouterInterface;

/** @var RouterInterface $router */
$result = $router->match($request);

$response = match ($result->status) {
    MatchStatus::MATCHED => $handlerResolver
        ->resolve($result->route->handler)
        ->handle($request->withAttribute(RouteResult::ATTRIBUTE, $result)),
    MatchStatus::NOT_FOUND         => $responseFactory->createResponse(404),
    MatchStatus::METHOD_NOT_ALLOWED => $responseFactory->createResponse(405),
};
```

## What's inside

| Namespace | What it provides |
|-----------|------------------|
| `Milpa\Http` | `HttpMethod` — the typed HTTP-verb vocabulary |
| `Milpa\Http\Routing` | `Route` (+ `#[Route]` attribute), `RouteResult`, `MatchStatus`, `HandlerReference`, `RouterInterface`, `HandlerResolverInterface`, `MiddlewareResolverInterface`, `UrlGeneratorInterface`, `UrlReferenceType` |
| `Milpa\Http\Exceptions` | `RoutingExceptionInterface` (marker) + `RouteNotFoundException`, `MissingRouteParametersException` (reverse-routing only) |

Every public symbol carries a DocBlock. A match miss is never an exception — it is
`RouteResult::notFound()`; the exceptions are raised only by URL generation, where an unknown
route name or a missing parameter is a bug in the caller.

## Requirements

- PHP **≥ 8.3**
- [`milpa/core`](https://packagist.org/packages/milpa/core) **^0.2**
- [`psr/http-message`](https://packagist.org/packages/psr/http-message) **^2.0**,
  [`psr/http-server-handler`](https://packagist.org/packages/psr/http-server-handler) **^1.0**,
  [`psr/http-server-middleware`](https://packagist.org/packages/psr/http-server-middleware) **^1.0**

## Documentation

**Full API reference: [getmilpa.github.io/http](https://getmilpa.github.io/http/)** — generated
straight from the source DocBlocks and dressed with the Milpa design system.

## Contributing

Contributions are welcome — see [CONTRIBUTING.md](CONTRIBUTING.md). Please report security
issues via [SECURITY.md](SECURITY.md), and note that this project follows a
[Code of Conduct](CODE_OF_CONDUCT.md).

## License

[Apache-2.0](LICENSE) © TeamX Agency.

---

Milpa is designed, built, and maintained by **[TeamX Agency](https://teamx.agency/?utm_source=github&utm_medium=readme&utm_campaign=milpa&utm_content=http)**.

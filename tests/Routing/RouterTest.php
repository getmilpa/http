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

namespace Milpa\Http\Tests\Routing;

use Milpa\Http\HttpMethod;
use Milpa\Http\Routing\HandlerReference;
use Milpa\Http\Routing\MatchStatus;
use Milpa\Http\Routing\Route;
use Milpa\Http\Routing\Router;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

final class RouterTest extends TestCase
{
    public function testMatchesAnExactSegment(): void
    {
        $route = new Route('/ping', HttpMethod::GET, handler: HandlerReference::action(self::class));
        $router = new Router($route);

        $result = $router->match(new ServerRequest('GET', '/ping'));

        $this->assertSame(MatchStatus::MATCHED, $result->status);
        $this->assertSame($route, $result->route);
        $this->assertSame([], $result->parameters);
    }

    public function testExtractsAPlaceholderParameter(): void
    {
        $route = new Route('/posts/{id}', HttpMethod::GET, handler: HandlerReference::action(self::class));
        $router = new Router($route);

        $result = $router->match(new ServerRequest('GET', '/posts/42'));

        $this->assertTrue($result->isMatched());
        $this->assertSame('42', $result->parameter('id'));
    }

    public function testExtractsMultiplePlaceholderParameters(): void
    {
        $route = new Route('/users/{userId}/posts/{postId}', HttpMethod::GET, handler: HandlerReference::action(self::class));
        $router = new Router($route);

        $result = $router->match(new ServerRequest('GET', '/users/7/posts/99'));

        $this->assertTrue($result->isMatched());
        $this->assertSame('7', $result->parameter('userId'));
        $this->assertSame('99', $result->parameter('postId'));
    }

    public function testNotFoundWhenNoRouteMatchesThePath(): void
    {
        $router = new Router();

        $result = $router->match(new ServerRequest('GET', '/nope'));

        $this->assertSame(MatchStatus::NOT_FOUND, $result->status);
        $this->assertFalse($result->isMatched());
        $this->assertNull($result->route);
    }

    public function testNotFoundWhenSegmentCountDiffers(): void
    {
        $route = new Route('/posts/{id}', HttpMethod::GET, handler: HandlerReference::action(self::class));
        $router = new Router($route);

        $result = $router->match(new ServerRequest('GET', '/posts/42/comments'));

        $this->assertSame(MatchStatus::NOT_FOUND, $result->status);
    }

    public function testMethodNotAllowedCarriesTheAllowedVerbs(): void
    {
        $route = new Route('/posts', [HttpMethod::GET, HttpMethod::POST], handler: HandlerReference::action(self::class));
        $router = new Router($route);

        $result = $router->match(new ServerRequest('DELETE', '/posts'));

        $this->assertSame(MatchStatus::METHOD_NOT_ALLOWED, $result->status);
        $this->assertFalse($result->isMatched());
        $this->assertSame([HttpMethod::GET, HttpMethod::POST], $result->allowedMethods);
    }

    public function testMethodNotAllowedAggregatesVerbsAcrossRoutesSharingAPath(): void
    {
        $get = new Route('/posts', HttpMethod::GET, handler: HandlerReference::action(self::class));
        $post = new Route('/posts', HttpMethod::POST, handler: HandlerReference::action(self::class));
        $router = new Router($get, $post);

        $result = $router->match(new ServerRequest('DELETE', '/posts'));

        $this->assertSame(MatchStatus::METHOD_NOT_ALLOWED, $result->status);
        $this->assertSame([HttpMethod::GET, HttpMethod::POST], $result->allowedMethods);
    }

    public function testATrailingSlashMatchesTheSameRouteAsWithoutIt(): void
    {
        $route = new Route('/posts', HttpMethod::GET, handler: HandlerReference::action(self::class));
        $router = new Router($route);

        $result = $router->match(new ServerRequest('GET', '/posts/'));

        $this->assertTrue($result->isMatched());
        $this->assertSame($route, $result->route);
    }

    public function testTheRootPathMatches(): void
    {
        $route = new Route('/', HttpMethod::GET, handler: HandlerReference::action(self::class));
        $router = new Router($route);

        $result = $router->match(new ServerRequest('GET', '/'));

        $this->assertTrue($result->isMatched());
    }

    public function testFirstMatchingRouteWinsWhenPathsOverlap(): void
    {
        $first = new Route('/posts/{id}', HttpMethod::GET, name: 'first', handler: HandlerReference::action(self::class));
        $second = new Route('/posts/{id}', HttpMethod::GET, name: 'second', handler: HandlerReference::action(self::class));
        $router = new Router($first, $second);

        $result = $router->match(new ServerRequest('GET', '/posts/1'));

        $this->assertSame('first', $result->route?->name);
    }

    public function testMethodMatchingIsCaseInsensitive(): void
    {
        $route = new Route('/ping', HttpMethod::GET, handler: HandlerReference::action(self::class));
        $router = new Router($route);

        $result = $router->match(new ServerRequest('get', '/ping'));

        $this->assertTrue($result->isMatched());
    }
}

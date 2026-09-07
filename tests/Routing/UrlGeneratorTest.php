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

namespace Milpa\Http\Tests\Routing;

use Milpa\Http\Exceptions\MissingRouteParametersException;
use Milpa\Http\Exceptions\RouteNotFoundException;
use Milpa\Http\HttpMethod;
use Milpa\Http\Routing\Route;
use Milpa\Http\Routing\Router;
use Milpa\Http\Routing\UrlGenerator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/** A route's NAME and its parameters in, its path out — over the table the router already holds. */
#[CoversClass(UrlGenerator::class)]
#[CoversClass(Router::class)]
final class UrlGeneratorTest extends TestCase
{
    public function testItBuildsThePathOfANamedRoute(): void
    {
        self::assertSame('/posts', $this->generator()->generate('posts.index'));
    }

    public function testItFillsPathParameters(): void
    {
        self::assertSame('/posts/7', $this->generator()->generate('posts.show', ['id' => 7]));
        self::assertSame('/users/3/posts/9', $this->generator()->generate('users.posts', ['userId' => 3, 'postId' => 9]));
    }

    public function testSurplusParametersBecomeAQueryString(): void
    {
        self::assertSame('/posts/7?page=2', $this->generator()->generate('posts.show', ['id' => 7, 'page' => 2]));
        self::assertSame('/posts?tag=milpa', $this->generator()->generate('posts.index', ['tag' => 'milpa']));
    }

    public function testAParameterValueIsEncodedRatherThanTrusted(): void
    {
        // A value that would change the SHAPE of the path must not be able to: an id of `../admin`
        // building `/posts/../admin` would be a path traversal handed out by the framework itself.
        self::assertSame('/posts/..%2Fadmin', $this->generator()->generate('posts.show', ['id' => '../admin']));
    }

    public function testAMissingPathParameterIsRefusedByNameRatherThanGuessed(): void
    {
        $this->expectException(MissingRouteParametersException::class);
        $this->expectExceptionMessageMatches('/id/');

        // A URL with a literal {id} in it is a bug that travels; refusing is the only honest answer.
        $this->generator()->generate('posts.show');
    }

    public function testAnUnknownRouteNameIsRefused(): void
    {
        $this->expectException(RouteNotFoundException::class);
        $this->expectExceptionMessageMatches('/nope/');

        $this->generator()->generate('nope');
    }

    public function testAnUnnamedRouteIsSkippedRatherThanBreakingTheIndex(): void
    {
        // Naming is optional on a Route, so a table with one unnamed route must still be indexable —
        // otherwise reverse routing is impossible for every app that has one.
        $router = new Router(
            new Route(path: '/anon', methods: HttpMethod::GET),
            new Route(path: '/named', methods: HttpMethod::GET, name: 'named'),
        );

        self::assertSame('/named', (new UrlGenerator($router))->generate('named'));
    }

    public function testTheRouterHandsBackTheTableItMatchesAgainst(): void
    {
        // The accessor this needed: the Kernel builds the table and, until now, nobody could read it.
        $routes = $this->router()->routes();

        self::assertCount(3, $routes);
        self::assertSame('/posts', $routes[0]->path);
    }

    private function generator(): UrlGenerator
    {
        return new UrlGenerator($this->router());
    }

    private function router(): Router
    {
        return new Router(
            new Route(path: '/posts', methods: HttpMethod::GET, name: 'posts.index'),
            new Route(path: '/posts/{id}', methods: HttpMethod::GET, name: 'posts.show'),
            new Route(path: '/users/{userId}/posts/{postId}', methods: HttpMethod::GET, name: 'users.posts'),
        );
    }
}

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
use Milpa\Http\Routing\Route;
use PHPUnit\Framework\TestCase;

final class RouteTest extends TestCase
{
    public function testSingleMethodIsNormalizedToList(): void
    {
        $route = new Route('/users', HttpMethod::GET);

        $this->assertSame([HttpMethod::GET], $route->methods);
        $this->assertTrue($route->allows(HttpMethod::GET));
        $this->assertFalse($route->allows(HttpMethod::POST));
    }

    public function testMethodListIsPreserved(): void
    {
        $route = new Route('/users', [HttpMethod::GET, HttpMethod::POST]);

        $this->assertTrue($route->allows(HttpMethod::GET));
        $this->assertTrue($route->allows(HttpMethod::POST));
    }

    public function testDefaultsToGetAndUnbound(): void
    {
        $route = new Route('/users');

        $this->assertSame([HttpMethod::GET], $route->methods);
        $this->assertFalse($route->isBound());
        $this->assertNull($route->handler);
    }

    public function testWithHandlerBindsImmutably(): void
    {
        $route = new Route('/users', name: 'users.index');
        $bound = $route->withHandler(HandlerReference::action('App\\Users'));

        $this->assertFalse($route->isBound());   // original untouched
        $this->assertTrue($bound->isBound());
        $this->assertSame('users.index', $bound->name);
        $this->assertNotSame($route, $bound);
    }

    public function testWithNameIsImmutable(): void
    {
        $route = new Route('/x');
        $named = $route->withName('x.route');

        $this->assertNull($route->name);
        $this->assertSame('x.route', $named->name);
    }

    public function testTheVerbFactoriesProduceTheValueTheConstructorProduces(): void
    {
        $handler = new HandlerReference('App\\Settings', 'save');

        $this->assertEquals(
            new Route('/settings', HttpMethod::GET, 'settings.show', [], $handler),
            Route::get('/settings', ['App\\Settings', 'save'], 'settings.show'),
        );
        $this->assertEquals(
            new Route('/settings', HttpMethod::POST, 'settings.save', [], $handler),
            Route::post('/settings', ['App\\Settings', 'save'], 'settings.save'),
        );
    }

    public function testAVerbFactoryRouteIsUnnamedAndUngatedUnlessSaidSo(): void
    {
        $route = Route::post('/x', ['App\\Act', 'run']);

        $this->assertNull($route->name);
        $this->assertSame([], $route->middleware);
        $this->assertSame([HttpMethod::POST], $route->methods);
        $this->assertSame('App\\Act::run', (string) $route->handler);
    }

    public function testWithMiddlewareIsImmutable(): void
    {
        $route = Route::get('/x', ['App\\Act', 'run']);
        $gated = $route->withMiddleware(['App\\Gate']);

        $this->assertSame([], $route->middleware);
        $this->assertSame(['App\\Gate'], $gated->middleware);
        $this->assertSame($route->handler, $gated->handler);
        $this->assertSame($route->methods, $gated->methods);
    }

    public function testBehindPrependsTheGateToEveryRouteAndKeepsWhatEachCarried(): void
    {
        $routes = Route::behind(
            ['App\\Door'],
            Route::get('/a', ['App\\A', 'index'], 'a'),
            Route::post('/b', ['App\\B', 'save'], 'b')->withMiddleware(['App\\Own']),
        );

        $this->assertCount(2, $routes);
        $this->assertSame(['App\\Door'], $routes[0]->middleware);
        $this->assertSame(['App\\Door', 'App\\Own'], $routes[1]->middleware);
        $this->assertSame('a', $routes[0]->name);
        $this->assertSame('/b', $routes[1]->path);
        $this->assertSame([HttpMethod::POST], $routes[1]->methods);
        $this->assertSame('App\\B::save', (string) $routes[1]->handler);
    }

    public function testBehindWithNoRoutesIsAnEmptyList(): void
    {
        $this->assertSame([], Route::behind(['App\\Door']));
    }

    public function testIsARepeatableMethodAttribute(): void
    {
        $attributes = (new \ReflectionClass(Route::class))->getAttributes(\Attribute::class);

        $this->assertNotEmpty($attributes);
        $this->assertSame(
            \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE,
            $attributes[0]->newInstance()->flags,
        );
    }
}

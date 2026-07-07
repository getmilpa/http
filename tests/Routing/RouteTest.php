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

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
use Milpa\Http\Routing\MatchStatus;
use Milpa\Http\Routing\Route;
use Milpa\Http\Routing\RouteResult;
use PHPUnit\Framework\TestCase;

final class RouteResultTest extends TestCase
{
    public function testMatchedCarriesRouteAndArguments(): void
    {
        $route = new Route('/users/{id}', HttpMethod::GET);
        $result = RouteResult::matched($route, ['id' => '42']);

        $this->assertTrue($result->isMatched());
        $this->assertSame(MatchStatus::MATCHED, $result->status);
        $this->assertSame($route, $result->route);
        $this->assertSame('42', $result->parameter('id'));
        $this->assertNull($result->parameter('missing'));
        $this->assertSame('fallback', $result->parameter('missing', 'fallback'));
    }

    public function testNotFound(): void
    {
        $result = RouteResult::notFound();

        $this->assertFalse($result->isMatched());
        $this->assertSame(MatchStatus::NOT_FOUND, $result->status);
        $this->assertNull($result->route);
        $this->assertSame([], $result->allowedMethods);
    }

    public function testMethodNotAllowedCarriesAllowedVerbs(): void
    {
        $result = RouteResult::methodNotAllowed([HttpMethod::GET, HttpMethod::HEAD]);

        $this->assertFalse($result->isMatched());
        $this->assertSame(MatchStatus::METHOD_NOT_ALLOWED, $result->status);
        $this->assertSame([HttpMethod::GET, HttpMethod::HEAD], $result->allowedMethods);
        $this->assertNull($result->route);
    }

    public function testAttributeKeyIsTheClassName(): void
    {
        $this->assertSame(RouteResult::class, RouteResult::ATTRIBUTE);
    }
}

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

namespace Milpa\Http\Tests\Exceptions;

use Milpa\Exceptions\MilpaExceptionInterface;
use Milpa\Http\Exceptions\MissingRouteParametersException;
use Milpa\Http\Exceptions\RouteNotFoundException;
use Milpa\Http\Exceptions\RoutingExceptionInterface;
use PHPUnit\Framework\TestCase;

final class RoutingExceptionsTest extends TestCase
{
    public function testRouteNotFoundNamesTheRoute(): void
    {
        $exception = RouteNotFoundException::forName('users.show');

        $this->assertStringContainsString('users.show', $exception->getMessage());
        $this->assertInstanceOf(RoutingExceptionInterface::class, $exception);
        $this->assertInstanceOf(MilpaExceptionInterface::class, $exception);
        $this->assertInstanceOf(\Throwable::class, $exception);
    }

    public function testMissingParametersListsThemAll(): void
    {
        $exception = MissingRouteParametersException::forRoute('users.show', ['id', 'slug']);

        $this->assertStringContainsString('users.show', $exception->getMessage());
        $this->assertStringContainsString('id', $exception->getMessage());
        $this->assertStringContainsString('slug', $exception->getMessage());
        $this->assertInstanceOf(RoutingExceptionInterface::class, $exception);
    }
}

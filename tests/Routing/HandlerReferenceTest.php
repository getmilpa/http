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

use Milpa\Http\Routing\HandlerReference;
use PHPUnit\Framework\TestCase;

final class HandlerReferenceTest extends TestCase
{
    public function testActionDefaultsToInvoke(): void
    {
        $ref = HandlerReference::action('App\\ShowUser');

        $this->assertSame('App\\ShowUser', $ref->controller);
        $this->assertSame('__invoke', $ref->method);
        $this->assertSame('App\\ShowUser::__invoke', (string) $ref);
    }

    public function testMethodReference(): void
    {
        $ref = HandlerReference::method('App\\UserController', 'show');

        $this->assertSame('App\\UserController', $ref->controller);
        $this->assertSame('show', $ref->method);
        $this->assertSame('App\\UserController::show', (string) $ref);
    }
}

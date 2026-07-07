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

namespace Milpa\Http\Tests;

use Milpa\Http\HttpMethod;
use PHPUnit\Framework\TestCase;

final class HttpMethodTest extends TestCase
{
    public function testSafeMethods(): void
    {
        $this->assertTrue(HttpMethod::GET->isSafe());
        $this->assertTrue(HttpMethod::HEAD->isSafe());
        $this->assertTrue(HttpMethod::OPTIONS->isSafe());
        $this->assertTrue(HttpMethod::TRACE->isSafe());
        $this->assertFalse(HttpMethod::POST->isSafe());
        $this->assertFalse(HttpMethod::DELETE->isSafe());
    }

    public function testIdempotentMethods(): void
    {
        $this->assertTrue(HttpMethod::GET->isIdempotent());
        $this->assertTrue(HttpMethod::PUT->isIdempotent());
        $this->assertTrue(HttpMethod::DELETE->isIdempotent());
        $this->assertFalse(HttpMethod::POST->isIdempotent());
        $this->assertFalse(HttpMethod::PATCH->isIdempotent());
    }

    public function testBackedByUppercaseVerb(): void
    {
        $this->assertSame('GET', HttpMethod::GET->value);
        $this->assertSame(HttpMethod::POST, HttpMethod::from('POST'));
        $this->assertNull(HttpMethod::tryFrom('BOGUS'));
    }
}

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
use PHPUnit\Framework\Attributes\DataProvider;
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

    public function testOfNamesAMethodByThePair(): void
    {
        $this->assertSame('App\\UserController::show', (string) HandlerReference::of(['App\\UserController', 'show']));
    }

    /**
     * @return iterable<string, array{array<mixed>}>
     */
    public static function malformedHandlers(): iterable
    {
        yield 'empty' => [[]];
        yield 'one element' => [['App\\Only']];
        yield 'three elements' => [['App\\C', 'a', 'b']];
        yield 'keyed pair' => [['controller' => 'App\\C', 'method' => 'a']];
        yield 'non-string method' => [['App\\C', 7]];
        yield 'empty class' => [['', 'a']];
        yield 'empty method' => [['App\\C', '']];
        yield 'bytes that are not UTF-8 still get the documented refusal' => [["\xff\xfe", 'a', 'b']];
    }

    /**
     * @param array<mixed> $handler
     */
    #[DataProvider('malformedHandlers')]
    public function testOfRefusesAMalformedHandlerByShape(array $handler): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('A handler is named as [Controller::class, \'method\']');

        HandlerReference::of($handler);
    }

    public function testMethodReference(): void
    {
        $ref = HandlerReference::method('App\\UserController', 'show');

        $this->assertSame('App\\UserController', $ref->controller);
        $this->assertSame('show', $ref->method);
        $this->assertSame('App\\UserController::show', (string) $ref);
    }
}

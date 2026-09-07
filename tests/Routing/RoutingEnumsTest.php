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

use Milpa\Http\Routing\MatchStatus;
use Milpa\Http\Routing\UrlReferenceType;
use PHPUnit\Framework\TestCase;

final class RoutingEnumsTest extends TestCase
{
    public function testMatchStatusValues(): void
    {
        $this->assertSame('matched', MatchStatus::MATCHED->value);
        $this->assertSame('not_found', MatchStatus::NOT_FOUND->value);
        $this->assertSame('method_not_allowed', MatchStatus::METHOD_NOT_ALLOWED->value);
    }

    /**
     * One case, and the count is the assertion.
     *
     * Three cases were removed because nothing in this framework holds the request context they need
     * — a scheme, a host, a base path. Pinning the COUNT is what keeps a fourth from being declared
     * again before something can render it: a reference type that cannot be rendered is a setting
     * whose value is ignored (greenhouse decisions/0215).
     */
    public function testUrlReferenceTypeOffersOnlyWhatThisFrameworkCanRender(): void
    {
        $this->assertCount(1, UrlReferenceType::cases());
        $this->assertSame(UrlReferenceType::ABSOLUTE_PATH, UrlReferenceType::cases()[0]);
    }
}

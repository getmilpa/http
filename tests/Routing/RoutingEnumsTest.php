<?php

/**
 * This file is part of Milpa HTTP — the web tier of the Milpa PHP framework.
 *
 * (c) TeamX Agency — https://teamx.agency <hola@teamx.agency>
 *
 * @license Apache-2.0
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

    public function testUrlReferenceTypeCases(): void
    {
        $this->assertCount(4, UrlReferenceType::cases());
        $this->assertContains(UrlReferenceType::ABSOLUTE_PATH, UrlReferenceType::cases());
    }
}

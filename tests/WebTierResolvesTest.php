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

namespace Milpa\Http\Tests;

use PHPUnit\Framework\TestCase;

final class WebTierResolvesTest extends TestCase
{
    /**
     * Every public contract autoloads under the flattened `Milpa\Http` namespace.
     */
    public function testContractSurfaceAutoloads(): void
    {
        $surface = [
            \Milpa\Http\HttpMethod::class,
            \Milpa\Http\Routing\Route::class,
            \Milpa\Http\Routing\RouteResult::class,
            \Milpa\Http\Routing\MatchStatus::class,
            \Milpa\Http\Routing\HandlerReference::class,
            \Milpa\Http\Routing\RouterInterface::class,
            \Milpa\Http\Routing\HandlerResolverInterface::class,
            \Milpa\Http\Routing\MiddlewareResolverInterface::class,
            \Milpa\Http\Routing\UrlGeneratorInterface::class,
            \Milpa\Http\Routing\UrlReferenceType::class,
            \Milpa\Http\Exceptions\RoutingExceptionInterface::class,
            \Milpa\Http\Exceptions\RouteNotFoundException::class,
            \Milpa\Http\Exceptions\MissingRouteParametersException::class,
        ];

        foreach ($surface as $fqcn) {
            $this->assertTrue(
                class_exists($fqcn) || interface_exists($fqcn) || enum_exists($fqcn),
                "Expected {$fqcn} to autoload.",
            );
        }
    }

    /**
     * The redesign dropped templating and lifecycle events from the http tier — the old
     * Medusa-derived contracts must not have leaked back into the package.
     */
    public function testLegacyTemplatingAndEventContractsAreGone(): void
    {
        $this->assertFalse(interface_exists('Milpa\\Http\\Interfaces\\TemplateEngineInterface'));
        $this->assertFalse(enum_exists('Milpa\\Http\\Enums\\Events'));
        $this->assertFalse(class_exists('Milpa\\Http\\RouteDefinition'));
    }
}

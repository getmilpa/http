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

namespace Milpa\Http\Exceptions;

use Milpa\Exceptions\MilpaExceptionInterface;

/**
 * Marker implemented by every exception milpa/http throws, so consumers can catch all
 * Milpa-originated HTTP errors uniformly. Extends the framework-wide core marker.
 */
interface RoutingExceptionInterface extends MilpaExceptionInterface
{
}

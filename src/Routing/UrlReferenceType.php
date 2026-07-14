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

namespace Milpa\Http\Routing;

/**
 * How {@see UrlGeneratorInterface} renders a generated reference — the typed replacement for
 * the integer flags routers traditionally use.
 */
enum UrlReferenceType
{
    /** A full absolute URL: `https://host/base/path`. */
    case ABSOLUTE_URL;

    /** An absolute path from the host root: `/base/path` (the default). */
    case ABSOLUTE_PATH;

    /** A scheme-relative network path: `//host/base/path`. */
    case NETWORK_PATH;

    /** A path relative to the current request: `../path`. */
    case RELATIVE_PATH;
}

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

namespace Milpa\Http\Routing;

/**
 * The three possible outcomes of matching a request against the route table, so the
 * pipeline branches with an exhaustive `match()` instead of nullable/boolean guessing.
 * String-backed for logging and telemetry.
 */
enum MatchStatus: string
{
    /** A route matched both the path and the method. */
    case MATCHED = 'matched';

    /** No route matched the path — the caller answers 404. */
    case NOT_FOUND = 'not_found';

    /** The path matched but not the method — the caller answers 405 with an `Allow` header. */
    case METHOD_NOT_ALLOWED = 'method_not_allowed';
}

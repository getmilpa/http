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
 *
 * ── THREE CASES WERE REMOVED, AND WHY ───────────────────────────────────────────────────────────
 *
 * `ABSOLUTE_URL`, `NETWORK_PATH` and `RELATIVE_PATH` are gone. Each needs a REQUEST CONTEXT — a
 * scheme, a host, a base path, the path being rendered from — and **nothing in this framework holds
 * any of it**: measured across the routing, runtime and web packages, zero occurrences of a base
 * path, a `SCRIPT_NAME`, a request context or an `app.url` key, and the skeleton mounts at `/`.
 *
 * They were declared before anything could render them, so an author could ask for an absolute URL
 * and get whatever the default was. A declaration a reader can set and nothing obeys is worse than
 * an absent feature, because it LOOKS like a feature — the same rule that took `Route::$host` out of
 * this package in the same arc (greenhouse decisions/0213 row 5, decisions/0215).
 *
 * Re-adding one is additive, the day someone proposes it WITH the context that renders it.
 */
enum UrlReferenceType
{
    /** An absolute path from the host root: `/path` — the only reference this framework can render. */
    case ABSOLUTE_PATH;
}

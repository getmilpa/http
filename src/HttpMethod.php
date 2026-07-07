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

namespace Milpa\Http;

/**
 * The typed HTTP-verb vocabulary.
 *
 * Replaces every stringly-typed method ('GET', 'GET|POST', the 'ALL' magic string):
 * routes, the `#[Route]` attribute, and match results all speak this enum, never a raw
 * string. A zero-dependency primitive at the package root because every routing type uses it.
 */
enum HttpMethod: string
{
    case GET = 'GET';
    case HEAD = 'HEAD';
    case POST = 'POST';
    case PUT = 'PUT';
    case PATCH = 'PATCH';
    case DELETE = 'DELETE';
    case OPTIONS = 'OPTIONS';
    case TRACE = 'TRACE';
    case CONNECT = 'CONNECT';

    /**
     * Whether the method is "safe" — read-only, with no expected server state change
     * (RFC 9110): GET, HEAD, OPTIONS and TRACE.
     */
    public function isSafe(): bool
    {
        return match ($this) {
            self::GET, self::HEAD, self::OPTIONS, self::TRACE => true,
            default => false,
        };
    }

    /**
     * Whether repeating the request has the same effect as making it once (RFC 9110):
     * every safe method, plus PUT and DELETE.
     */
    public function isIdempotent(): bool
    {
        return $this->isSafe() || match ($this) {
            self::PUT, self::DELETE => true,
            default => false,
        };
    }
}

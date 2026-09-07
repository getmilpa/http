# Upgrading

## 0.3.0 — three `Route` fields the matcher never read are gone

`Route::$host`, `Route::$priority` and `Route::$defaults` were **removed** from the value object and its
constructor.

Nothing read them. Measured across the framework's 37 packages: zero reads of `->host`, zero of `->priority`,
zero of `->defaults`, and no call site anywhere passed one. The router matched on path and method and ignored
all three — so a route could declare a host, a priority and defaults, and none of it did anything.

**A declaration a reader can set and nothing obeys is worse than an absent feature, because it looks like a
feature.** Whoever wants host matching or an explicit priority proposes them *with the matcher that honours
them* (greenhouse `decisions/0213` row 5, `decisions/0215`).

**What breaks.** Every route in this family is constructed with named arguments, so nothing here changed. If
you passed them POSITIONALLY — `new Route($path, $methods, $name, $host, $priority, $defaults, $middleware)` —
your call now binds `$host` where `$middleware` is expected and fails with a `TypeError`. That is deliberate:
this fails loudly rather than quietly binding your middleware into a slot that no longer exists.

Named arguments (`middleware:`, `handler:`) are unaffected.

## 0.4.0 — reverse routing arrives, and three reference types leave

`UrlGeneratorInterface` finally has an implementation: `UrlGenerator`, built over `Router::routes()` — a new
read-only accessor, because the table the Kernel assembles was reachable by nobody.

**`UrlReferenceType::ABSOLUTE_URL`, `NETWORK_PATH` and `RELATIVE_PATH` were removed.** Each needs a request
context — a scheme, a host, a base path, the path being rendered from — and nothing in this framework holds any
of it: zero occurrences of a base path, a `SCRIPT_NAME`, a request context or an `app.url` key across the
routing, runtime and web packages, and the skeleton mounts at `/`.

They were declared before anything could render them, so an author could ask for an absolute URL and silently
get a path. A declaration a reader can set and nothing obeys is worse than an absent feature, because it looks
like one — the same rule that removed `Route::$host` in 0.3.0. Re-adding one is additive, the day someone
proposes it with the context that renders it.

**If you referenced one of the three:** there was nothing to render them, so any call site was already getting an
absolute path. Drop the argument, or pass `UrlReferenceType::ABSOLUTE_PATH` explicitly.


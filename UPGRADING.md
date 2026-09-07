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

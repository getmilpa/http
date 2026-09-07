# Changelog

## [0.4.0](https://github.com/getmilpa/http/compare/v0.3.0...v0.4.0) (2026-09-07)


### ⚠ BREAKING CHANGES

* UrlReferenceType::ABSOLUTE_URL, NETWORK_PATH and RELATIVE_PATH are removed. Each needs a request context — scheme, host, base path — and nothing in this framework holds any of it: zero occurrences of a base path, SCRIPT_NAME, a request context or an app.url key across the routing, runtime and web packages. They were declared before anything could render them, so asking for an absolute URL silently returned a path. See UPGRADING.md.

### Features

* reverse routing over the table the router already holds ([#19](https://github.com/getmilpa/http/issues/19)) ([74481eb](https://github.com/getmilpa/http/commit/74481eba408bf641712f70979d5e7e7434615649))

## [0.3.0](https://github.com/getmilpa/http/compare/v0.2.1...v0.3.0) (2026-09-07)


### ⚠ BREAKING CHANGES

* Route::$host, Route::$priority and Route::$defaults are removed from the value object and its constructor. Nothing read them — measured across the framework's 37 packages, zero reads and no call site passed one. Callers using named arguments are unaffected; a positional caller now gets a TypeError rather than a silent misbinding of $middleware into a slot that no longer exists. See UPGRADING.md.

### Features

* release the Route field retirement as the breaking change it is ([#17](https://github.com/getmilpa/http/issues/17)) ([7069c14](https://github.com/getmilpa/http/commit/7069c145d7cc2b662aac0b498804de8826ba82f1))

## [0.1.6](https://github.com/getmilpa/http/compare/v0.1.5...v0.1.6) (2026-08-01)


### Bug Fixes

* **deps:** el pin de milpa/core acepta la linea 0.7 ([1403640](https://github.com/getmilpa/http/commit/1403640a161d3f1a8c32ada426ee750a162a1066))

## [0.1.5](https://github.com/getmilpa/http/compare/v0.1.4...v0.1.5) (2026-07-12)


### Bug Fixes

* receive milpa/core 0.6 — pin bump ([2ef9ca0](https://github.com/getmilpa/http/commit/2ef9ca052bd1e66bb2209ca56b2674f29376adae))

## [0.1.4](https://github.com/getmilpa/http/compare/v0.1.3...v0.1.4) (2026-07-09)


### Features

* ship a concrete Router ([95a796d](https://github.com/getmilpa/http/commit/95a796d4002fae2c6375c506154efe7785955eba))


### Miscellaneous Chores

* release 0.1.4 ([f01a20f](https://github.com/getmilpa/http/commit/f01a20f9efd10a395acf5fa3096d5d1090e8a654))

## [0.1.3](https://github.com/getmilpa/http/compare/v0.1.2...v0.1.3) (2026-07-08)


### Bug Fixes

* require milpa/core ^0.5 ([7291776](https://github.com/getmilpa/http/commit/72917767fdbcdf65520544bc805f4857659da2a4))

## [0.1.2](https://github.com/getmilpa/http/compare/v0.1.1...v0.1.2) (2026-07-08)


### Bug Fixes

* require milpa/core ^0.4 ([57c2176](https://github.com/getmilpa/http/commit/57c21765897851f7dc44d0a53235eea8e62c0f97))

## [0.1.1](https://github.com/getmilpa/http/compare/v0.1.0...v0.1.1) (2026-07-08)


### Bug Fixes

* **docs:** family-coherent links + footer credit color on the docs site ([6efd209](https://github.com/getmilpa/http/commit/6efd20935f3a3277edf06839c47fd1199507c943))
* require milpa/core ^0.3 ([2013560](https://github.com/getmilpa/http/commit/2013560ac0b4142f0feba4fac4a592cec4201aae))
* require milpa/core ^0.4 ([57c2176](https://github.com/getmilpa/http/commit/57c21765897851f7dc44d0a53235eea8e62c0f97))

## [0.1.1](https://github.com/getmilpa/http/compare/v0.1.0...v0.1.1) (2026-07-07)


### Bug Fixes

* **docs:** family-coherent links + footer credit color on the docs site ([6efd209](https://github.com/getmilpa/http/commit/6efd20935f3a3277edf06839c47fd1199507c943))
* require milpa/core ^0.3 ([2013560](https://github.com/getmilpa/http/commit/2013560ac0b4142f0feba4fac4a592cec4201aae))

## 0.1.0 (2026-07-06)


### Features

* milpa/http initial public release ([87d329c](https://github.com/getmilpa/http/commit/87d329c46034a016406b0cdee7309021bd2ad376))

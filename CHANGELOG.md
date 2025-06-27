# CHANGELOG

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- MiddlewareBuilder (Laminas based)
- Router implements PSR/RequestHandlerInterface

### Changed
- **BC:** `Router::getHandler()` method renamed to `getHandlerWithRequestContainer`

## [0.0.4] - 2025-06-22

### Changed
- Allow psr/http-messages v1 usage

### Internal
- Use phpstan, configure test coverage

## [0.0.3] - 2025-06-22

### Removed
- Not required dependencies move to suggested and replaced with psr interfaces

### Internal
- Use php-cs-fixer

## [0.0.2] - 2025-06-17

### Added
- Path normalizing for trailing slash
- `OPTIONS` Handler prototype support
- Types Exceptions
- Handlers for not found and not allowed cases
- Handler instances support in configuration

### Changed
- Constructors arguments order

## [0.0.1] - 2025-06-11

### Added
- Router, RequestHandlerFactory and FastRoute backed implementation of basics

[Unreleased]: https://github.com/FreeElephants/psr-router/compare/0.0.4...HEAD
[0.0.4]: https://github.com/FreeElephants/psr-router/releases/tag/0.0.4
[0.0.3]: https://github.com/FreeElephants/psr-router/releases/tag/0.0.3
[0.0.2]: https://github.com/FreeElephants/psr-router/releases/tag/0.0.2
[0.0.1]: https://github.com/FreeElephants/psr-router/releases/tag/0.0.1

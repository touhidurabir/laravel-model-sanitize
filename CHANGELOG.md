# Changelog
All notable changes to this project will be documented in this file.

## [Unreleased]
- Laravel 11, 12 and 13 support added.
- PHPUnit 10/11/12 support for tests.
- Fixed fatal error on Laravel 12+/13 caused by `SanitizableQueryBuilder` method signatures (`firstOrCreate`, `firstOrNew`, `updateOrCreate`) being incompatible with the parent `Closure|array $values` parameter; `$values` given as a Closure is now sanitized too.
- Code style formatted to PSR-12 with Laravel Pint (`composer format` / `composer lint`).
- Replaced Travis CI with GitHub Actions (PHP 8.1 × Laravel 8/9/10 and PHP 8.2–8.4 × Laravel 11/12/13, plus a code style check).
- Tests no longer depend on their execution order: the static sanitization flag is reset before and after each test.
- Fixed the `ModelSanitize` facade: its `model-sanitize` container key was never bound, so any facade call failed.
  It now resolves to `Touhidurabir\ModelSanitize\ModelSanitizer`, which offers `sanitize($model, $attributes)` and
  `gibberish($model, $attributes)` for a model class name or instance.
- **Upgrade note:** apps that subclass `SanitizableQueryBuilder` and re-declare `updateOrCreate`, `firstOrCreate` or
  `firstOrNew` with `array $values` must drop that type (use `$values = []`). The parent parameter is now untyped, so
  the narrower `array` type is a fatal signature error; on Laravel 12/13 Laravel's own `Closure|array` signature
  requires this anyway. See the README section "Upgrade note: extending `SanitizableQueryBuilder`".


## [1.0.0] - 2021-08-14
- Initial release

## [1.0.1] - 2021-10-29
- Bug fix.

## [1.1.0] - 2021-11-15
- Bug fix.
- Readme Update
- Tests update.
- Sanitization runtime disable/enable feature added.

## [1.1.1] - 2022-04-22
- Merge pull request by [viicslen](https://github.com/viicslen) which set schema builder connection to the same connection as the model so that it can work properly with `Multi Tenant` application poplery.
- Readme Update

## [1.1.2] - 2023-04-01
- Added Laravel 9 and 10 support
# Changelog

All notable changes to `laravel-azampay` will be documented in this file.

## v1.1.0 - 2026-11-20

The first release since v1.0.0. It brings the package up to date with Laravel and fixes a callback problem that affected real payments.

### What this means for you

- **You can install on Laravel 12 and 13.** Laravel 10 and 11 keep working.
- **Callbacks stop being rejected.** AzamPay does not always send `submerchantAcc`, but the package required it and answered `422`. Those callbacks are now accepted. Operator names are no longer limited to a fixed list, so new or renamed operators get through.
- **PHP 8.2 is the minimum.** PHP 8.1 reached end of life in December 2025.

### What you need to do

```bash
composer require alphaolomi/laravel-azampay:^1.1

```
No code changes are needed. No public method changed. If you are on PHP 8.1, upgrade PHP first or stay on v1.0.0.

### Supported versions

| Package | Laravel | PHP | Status |
|---|---|---|---|
| 1.x | 10, 11, 12, 13 | 8.2 to 8.5 | Supported |
| 0.x | 9, 10 | 8.1 | Ended |

### Thanks

- @tufikhasan for Laravel 12 support (#48)
- @rohitgulam for finding and first fixing the `submerchantAcc` callback problem

### Technical changes

- Laravel 12 and 13 in `illuminate/contracts`; PHP `^8.2` (#48, #52)
- Callback route: `submerchantAcc` nullable, `operator` any string, with a test (#52)
- Tests and PHPStan run on pull requests; matrix covers PHP 8.2 to 8.5 and Laravel 10 to 13 on Ubuntu and Windows (#52)
- Dev tooling: Pest 2 to 4, Larastan 2 or 3, Testbench 8 to 11, PHPUnit config on the current schema (#52)
- Added `SECURITY.md` and `CONTRIBUTING.md` (#52)
- GitHub Actions updates (#44, #45, #49, #51)

**Full changelog**: https://github.com/alphaolomi/laravel-azampay/compare/v1.0.0...v1.1.0

## v1.0.0 - 2024-08-30

### 🎉  What's Changed

* docs: Update README.md by @alphaolomi in https://github.com/alphaolomi/laravel-azampay/pull/19
* Bump dependabot/fetch-metadata from 1.5.1 to 1.6.0 by @dependabot in https://github.com/alphaolomi/laravel-azampay/pull/21
* Bump actions/checkout from 3 to 4 by @dependabot in https://github.com/alphaolomi/laravel-azampay/pull/23
* Bump stefanzweifel/git-auto-commit-action from 4 to 5 by @dependabot in https://github.com/alphaolomi/laravel-azampay/pull/24
* Bump aglipanci/laravel-pint-action from 2.3.0 to 2.3.1 by @dependabot in https://github.com/alphaolomi/laravel-azampay/pull/25
* ci: Update testsing GH action by @alphaolomi in https://github.com/alphaolomi/laravel-azampay/pull/28
* Bump ramsey/composer-install from 2 to 3 by @dependabot in https://github.com/alphaolomi/laravel-azampay/pull/27
* Bump aglipanci/laravel-pint-action from 2.3.1 to 2.4 by @dependabot in https://github.com/alphaolomi/laravel-azampay/pull/30
* chore: add Laravel 11 by @alphaolomi in https://github.com/alphaolomi/laravel-azampay/pull/34
* chore: add Laravel 11 by @alphaolomi in https://github.com/alphaolomi/laravel-azampay/pull/35
* Bump dependabot/fetch-metadata from 1.6.0 to 2.1.0 by @dependabot in https://github.com/alphaolomi/laravel-azampay/pull/31
* chore(deps): bump dependabot/fetch-metadata from 2.1.0 to 2.2.0 by @dependabot in https://github.com/alphaolomi/laravel-azampay/pull/36

**Full Changelog**: https://github.com/alphaolomi/laravel-azampay/compare/v0.2.0...v1.0.0

## Unreleased

- Laravel 12 support (#48 by @tufikhasan) and Laravel 13 support
- PHP 8.2 is now the minimum; PHP 8.1 is end of life
- Callback route accepts callbacks without `submerchantAcc` (optional in AzamPay's API) and any operator name; thanks @rohitgulam for the report
- CI: tests and static analysis run on pull requests; matrix covers PHP 8.2 to 8.5 and Laravel 10 to 13
- Added SECURITY.md and CONTRIBUTING.md

### What's Changed

- **Laravel 11 Support** by @alphaolomi

## v0.1.0 - 2023-03-01

### What's Changed

- **Documentation Updates, Feature test and Disbursement endpoints** by @omakei in https://github.com/alphaolomi/laravel-azampay/pull/6
- Bump dependabot/fetch-metadata from 1.3.4 to 1.3.5 by @dependabot in https://github.com/alphaolomi/laravel-azampay/pull/2
- Bump ramsey/composer-install from 1 to 2 by @dependabot in https://github.com/alphaolomi/laravel-azampay/pull/3
- Bump aglipanci/laravel-pint-action from 1.0.0 to 2.1.0 by @dependabot in https://github.com/alphaolomi/laravel-azampay/pull/4
- Bump dependabot/fetch-metadata from 1.3.5 to 1.3.6 by @dependabot in https://github.com/alphaolomi/laravel-azampay/pull/5

### New Contributors

- @omakei made their first contribution in https://github.com/alphaolomi/laravel-azampay/pull/6

**Full Changelog**: https://github.com/alphaolomi/laravel-azampay/compare/v0.0.1...v0.1.0

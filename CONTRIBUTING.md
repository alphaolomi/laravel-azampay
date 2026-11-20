# Contributing

Thanks for helping. Issues and pull requests are welcome.

## Before you open a pull request

- Open an issue first for anything bigger than a fix, so the approach can be
  agreed before the work.
- One change per pull request.
- Add or update a test. The suite uses [Pest](https://pestphp.com) with recorded
  responses in `tests/stubs/responses`; no network calls.
- Run the checks locally:

```bash
composer test      # Pest
composer analyse   # PHPStan
composer format    # Pint
```

- Describe the change in the pull request and, if it changes behaviour, add a
  line under "Unreleased" in `CHANGELOG.md`.

## Supported versions

The package follows Laravel's support policy: the current major and the one
before it are supported, older ones are dropped at their security end date.
CI runs the full matrix on every pull request; keep it green.

## Reporting security issues

See `SECURITY.md`. Do not report security problems in public issues.

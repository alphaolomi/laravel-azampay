# Security Policy

## Supported versions

| Version | Laravel | PHP | Status |
|---------|---------|-----|--------|
| 1.x     | 10, 11, 12, 13 | 8.2 to 8.5 | Supported: features and fixes |
| 0.x     | 9, 10 | 8.1 | Ended: no fixes |

At most two major versions are supported at a time. When a new major version is
released, the previous one receives security and critical fixes for six months.

## Reporting a vulnerability

Please do not open a public issue for security problems. Use GitHub's private
vulnerability reporting on this repository ("Report a vulnerability" under the
Security tab). You will get an acknowledgement within a few days and a fix or a
mitigation plan as soon as the report is confirmed.

This package talks to a payment provider. Anything that could expose
credentials, let a forged callback through, or send money to the wrong place is
in scope.

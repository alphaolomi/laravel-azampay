# Security Policy

## Supported versions

| Version | Supported |
|---------|-----------|
| 1.x     | Yes       |
| 0.x     | No        |

## Reporting a vulnerability

Please do not open a public issue for security problems. Use GitHub's private
vulnerability reporting on this repository ("Report a vulnerability" under the
Security tab). You will get an acknowledgement within a few days and a fix or a
mitigation plan as soon as the report is confirmed.

This package talks to a payment provider. Anything that could expose
credentials, let a forged callback through, or send money to the wrong place is
in scope.

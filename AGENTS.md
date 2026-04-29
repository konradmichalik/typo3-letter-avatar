# AGENTS.md

This file provides guidance for AI coding agents working on this repository.

## Project Overview

`typo3_letter_avatar` is a TYPO3 extension that generates colorful backend user avatars from name initials. It also exposes a Fluid ViewHelper for frontend avatar rendering.

## Supported Versions (v2.x)

- TYPO3 v13.4 LTS and v14.x
- PHP 8.2+ (8.2, 8.3, 8.4, 8.5)
- Symfony Console ^7.0 || ^8.0
- Fluid ^4.2 || ^5.0

For the legacy v1.x line (TYPO3 v11.5 / v12.4 / v13.4 LTS, PHP 8.1–8.4) see `Documentation/Migration-v1-v2.md`.

## Repository Layout

- `Classes/` — extension PHP source (PSR-4: `KonradMichalik\Typo3LetterAvatar\`)
- `Configuration/` — TYPO3 configuration (Services.yaml, TCA, etc.)
- `Resources/` — fonts, icons, language files, public assets
- `Documentation/` — Markdown documentation (Configuration, Usage, Migration)
- `Tests/` — unit tests (`Tests/Unit/`), CGL config (`Tests/CGL/`), DDEV fixtures (`Tests/Acceptance/Fixtures/`)
- `ext_localconf.php`, `ext_emconf.php`, `ext_conf_template.txt` — TYPO3 extension manifests

## Common Commands

All CGL tooling lives under `Tests/CGL/` and is invoked via the `cgl` composer alias:

```bash
composer cgl lint        # PHP-CS-Fixer + composer-normalize + editorconfig-cli (dry-run)
composer cgl fix         # apply lint fixes
composer cgl sca         # PHPStan
composer cgl migration   # Rector
composer cgl analyze     # composer-dependency-analyser
```

Unit tests:

```bash
composer test             # PHPUnit (no coverage)
composer test:coverage    # PHPUnit with coverage
```

Inside DDEV (recommended for multi-version TYPO3 setups):

```bash
ddev composer cgl fix
ddev composer cgl sca
ddev composer test
ddev install 13           # provision TYPO3 v13 instance
ddev install 14           # provision TYPO3 v14 instance
```

## Coding Conventions

- PHP 8.2+ syntax (readonly classes, enums, constructor property promotion are encouraged)
- `declare(strict_types=1);` on every PHP file
- PSR-12 + project-specific PHP-CS-Fixer rules (see `Tests/CGL/`)
- PHPStan analysis must pass at the configured level
- New features ship with unit tests under `Tests/Unit/`

## Testing

- Unit tests: PHPUnit, run via `composer test`
- DDEV-based smoke testing across TYPO3 versions: `ddev install <version>` auto-imports backend user fixtures from `Tests/Acceptance/Fixtures/`
- Aim to keep coverage on new code; the suite currently includes name-edge-case tests (apostrophes, unicode, umlauts, long names)

## Branching

- `main` — released v1.x maintenance
- `2.x-dev` — active v2.x development
- Topic branches PR into the appropriate base

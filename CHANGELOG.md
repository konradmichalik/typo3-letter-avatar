# Changelog

All notable changes to this project will be documented in this file.

## [Unreleased]

### Added
- Support for TYPO3 v14
- Support for Symfony 8
- Support for Fluid v5
- Support for PHP 8.5
- Demo backend user fixtures for DDEV setup (auto-imported via `ddev install`)
- Extended unit test coverage with edge-case tests (apostrophes, unicode, long names, umlauts, two-char first names) matching demo backend user fixtures
- `Documentation/Migration-v1-v2.md` migration guide
- Compatibility matrix in `README.md`

### Changed
- Restructured CGL tooling to `Tests/CGL/` with own `composer.json`
- Migrated CGL to `konradmichalik/php-cs-fixer-preset` and `konradmichalik/phpstan-typo3-preset`
- Migrated fixture and sitepackage paths from `Tests/.typo3-setup/` to `Tests/Acceptance/Fixtures/` (DDEV addon convention)
- Update `konradmichalik/ddev-typo3-multi-version-extension` addon for fixture auto-discovery

### Removed
- Support for TYPO3 v11.5
- Support for TYPO3 v12.4
- Support for Symfony Console v5
- Support for Symfony Console v6
- Support for Fluid v2
- Support for PHP 8.1

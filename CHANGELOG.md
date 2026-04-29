# Changelog

All notable changes to this project will be documented in this file.

## [Unreleased]

### Added
- Support for TYPO3 v14
- Support for Symfony 8
- Support for Fluid v5
- Support for PHP 8.5
- Demo backend user fixtures for DDEV setup (auto-imported via `ddev install`)

### Changed
- Migrate fixture and sitepackage paths from `Tests/.typo3-setup/` to `Tests/Acceptance/Fixtures/` (DDEV addon convention)
- Update `konradmichalik/ddev-typo3-multi-version-extension` addon for fixture auto-discovery

### Removed
- Support for TYPO3 v11.5
- Support for TYPO3 v12.4
- Support for Symfony Console v5
- Support for Symfony Console v6
- Support for Fluid v2
- Support for PHP 8.1

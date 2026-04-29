# Migration Guide: v1 → v2

## Breaking Changes

### Dropped Support

- **TYPO3 v11.5** — upgrade your TYPO3 instance to v13 LTS (recommended) or v14, or stay on `konradmichalik/typo3-letter-avatar:^1.0`
- **TYPO3 v12.4** — same upgrade path
- **PHP 8.1** — upgrade your PHP runtime to 8.2 or later
- **Symfony Console v5 / v6** — typically transparent; no action required if you don't directly depend on these
- **Fluid v2.7** — only relevant if you've embedded letter-avatar's Fluid templates outside TYPO3

### Added

- **TYPO3 v14** support
- **PHP 8.5** support
- **Symfony 8** support
- **Fluid 5** support

## Upgrade Path

Standard composer upgrade:

```bash
composer require konradmichalik/typo3-letter-avatar:^2.0
```

Then clear TYPO3 caches: **Backend → Maintenance → Flush all caches**.

## API Compatibility

The user-facing API is unchanged. All existing extension configuration values, event listeners (`BackendUserAvatarConfigurationEvent`), and ViewHelper usage (`<la:avatar>`) continue to work as in v1.x. No template, TypoScript, or PHP migration is required for projects that use only the documented API.

If your project subclasses or directly references internal classes (under `KonradMichalik\Typo3LetterAvatar\`), review the v2 source — most internal types remain stable, but TYPO3-imposed v14 API changes may have rippled through.

## Tooling Notes (Contributors)

- All code-quality-gate (CGL) tooling now lives under `Tests/CGL/` with its own `composer.json`. Use:
  - `composer cgl lint` (PHP-CS-Fixer + composer-normalize + editorconfig-cli)
  - `composer cgl fix` (apply fixes)
  - `composer cgl sca` (PHPStan)
  - `composer cgl migration` (Rector)
  - `composer cgl analyze` (composer-dependency-analyser)
- Migrated to `konradmichalik/php-cs-fixer-preset` and `konradmichalik/phpstan-typo3-preset` (replaces `eliashaeussler/php-cs-fixer-config` and the standalone phpstan packages).
- `composer-require-checker` is still used in CI; `shipmonk/composer-dependency-analyser` is the local complement.
- Local DDEV setup uses the `konradmichalik/ddev-typo3-multi-version-extension` addon. Fixtures live under `Tests/Acceptance/Fixtures/` and are auto-imported on `ddev install <version>`.

## Maintenance Window for v1.x

The `1.x` branch receives security fixes only. Active maintenance is committed to TYPO3 v13/v14 + PHP 8.2-8.5 (the v2 supported set).

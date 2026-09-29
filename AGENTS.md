# AGENTS.md

## Project overview

`typo3_letter_avatar` is a TYPO3 extension that generates colorful backend user avatars from name initials. It also provides a Fluid ViewHelper for frontend avatar rendering.

- Package: `konradmichalik/typo3-letter-avatar`, namespace `KonradMichalik\Typo3LetterAvatar` (PSR-4, `Classes/`)
- v2.x: TYPO3 13.4 and 14.x, PHP 8.2 to 8.5, Symfony Console `^7.0 || ^8.0`, Fluid `^4.2 || ^5.0`
- The legacy v1.x line (TYPO3 11.5, 12.4, 13.4, PHP 8.1 to 8.4) is described in `Documentation/Migration-v1-v2.md`

## Structure

- `Classes/AvatarProvider/` backend avatar provider, `Classes/ViewHelpers/` frontend ViewHelper, `Classes/Command/` `ClearAvatarsCommand`
- `Classes/Image/` `Avatar` factory, `AbstractImageProvider` and `Driver/` (GD, Imagick, Gmagick)
- `Classes/Service/` `Colorize`, `BackendThemeResolver`, `Classes/Utility/`, `Classes/Enum/`, `Classes/Event/`
- `Configuration/` `Services.yaml`, TCA and more
- `Resources/` fonts, icons, language files, public assets
- `ext_localconf.php`, `ext_emconf.php`, `ext_conf_template.txt` extension manifests and default configuration
- `Tests/Unit/` PHPUnit tests, mirror `Classes/`
- `Tests/CGL/` separate Composer project with code style, static analysis and migration tooling
- `Tests/Acceptance/Fixtures/` backend user fixtures, imported by `ddev install`
- `Documentation/` Markdown docs (Configuration, Usage, Migration)
- `.ddev/` DDEV setup and commands to install TYPO3 13 and 14 test instances

## Architecture

- Entry points (`LetterAvatarProvider`, `AvatarViewHelper`, `ClearAvatarsCommand`) collect config via `ConfigurationUtility` and dispatch the PSR-14 `BackendUserAvatarConfigurationEvent`, so listeners can override config per user.
- `Image\Avatar::create()` is a static factory. It picks the driver from `$GLOBALS['TYPO3_CONF_VARS']['GFX']['processor']` or an explicit override and falls back to GD if the PHP extension is missing.
- All drivers extend `AbstractImageProvider`, which holds the shared config and computes a deterministic SHA-256 filename in `configToHash()`. A new config field must be added to `configToHash()`, otherwise cache keys collide and stale images are served.
- `Service\Colorize` colors by the `ColorMode` enum (`RANDOM`, `STRINGIFY`, `PAIRS`, `THEME`, `CUSTOM`), with palettes from `ext_localconf.php` merged with extension settings.
- Images are written to `imagePath` (default `/typo3temp/assets/avatars/`) and only re-rendered when the hashed file is missing.
- Configuration priority, highest first: event listener override, per-call argument or ViewHelper attribute, extension settings (`ext_conf_template.txt`), defaults in `$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['configuration']`.
- Always read config through `ConfigurationUtility::get($key, $enumClass?)`, never from the globals directly.
- Enums under `Classes/Enum/` implement `EnumInterface`. `ConfigurationUtility::get()` converts stored scalars to enum cases when given an enum class.

## Development commands

The project uses DDEV, which is recommended because host PHP often lacks `ext-intl` and `ext-mbstring`.

```bash
ddev start
ddev composer install
ddev install 13       # or 14, or all
ddev 13 typo3 cache:flush
```

All tooling lives in the nested Composer project `Tests/CGL/`. The root `composer.json` exposes it as the alias `cgl`, so `composer cgl <script>` runs a script of `Tests/CGL/composer.json`. Do not move these tools into the root. The root `config.allow-plugins` must still list `ergebnis/composer-normalize` and `move-elevator/composer-translation-validator`, because Composer validates it at root install time.

```bash
ddev composer cgl lint       # PHP CS Fixer, composer normalize, editorconfig (dry-run)
ddev composer cgl fix
ddev composer cgl sca        # PHPStan
ddev composer cgl migration  # Rector
ddev composer cgl analyze    # composer-dependency-analyser
```

Before pushing, run the full cycle inside DDEV. The shared CI workflow is strict and fails on header year drift, Rector changes and normalize differences. Commit anything it changes.

```bash
ddev composer cgl fix && ddev composer cgl sca && ddev composer cgl migration && ddev composer test
```

## Testing

PHPUnit unit tests in `Tests/Unit/` (`phpunit.xml`). The suite includes name edge cases (apostrophes, unicode, umlauts, long names). New features ship with unit tests.

```bash
ddev composer test                                   # without coverage
ddev composer test:coverage                          # XDEBUG_MODE=coverage, reports in .Build/coverage/
ddev composer test -- --filter ConfigurationTest     # single class or method
ddev composer test -- Tests/Unit/Utility/StringUtilityTest.php
```

CI runs the shared `tests-typo3` workflow on TYPO3 13.4 and 14.3, PHP 8.2 to 8.5, with `highest` and `lowest` dependencies.

## Code style and static analysis

- PHP 8.2 syntax (readonly classes, enums, constructor property promotion) and `declare(strict_types=1);` in every PHP file
- PHP CS Fixer with `konradmichalik/php-cs-fixer-preset`, config in `Tests/CGL/.php-cs-fixer.php`. The fixer generates the license header
- PHPStan level 8 with baseline, config in `Tests/CGL/phpstan.neon`
- Rector config in `Tests/CGL/rector.php`
- PHP support must stay aligned between `composer.json` `require.php` and `config.platform.php` (`8.2`, the lowest supported version)

## Git workflow

- Commit format: `<type>: <description>` with type one of `feat`, `fix`, `refactor`, `docs`, `test`, `chore`, `perf`, `ci`
- Single-line messages, no co-author trailers
- `main` is the v2.x development line. Topic branches open pull requests against `main`. Confirm which line a fix targets before opening a PR

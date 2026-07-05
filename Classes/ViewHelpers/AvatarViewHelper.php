<?php

declare(strict_types=1);

/*
 * This file is part of the "typo3_letter_avatar" TYPO3 CMS extension.
 *
 * (c) Konrad Michalik <hej@konradmichalik.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KonradMichalik\Typo3LetterAvatar\ViewHelpers;

use BackedEnum;
use InvalidArgumentException;
use KonradMichalik\Typo3LetterAvatar\Enum\{ColorMode, ImageFormat, Shape, Transform};
use KonradMichalik\Typo3LetterAvatar\Image\Avatar;
use KonradMichalik\Typo3LetterAvatar\Utility\ConfigurationUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

use function is_string;
use function sprintf;

/**
 * AvatarViewHelper.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class AvatarViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        $this->registerArgument(
            'name',
            'string',
            'Name for the avatar',
            false,
        );
        $this->registerArgument(
            'initials',
            'string',
            'Initials for the avatar',
            false,
        );
        $this->registerArgument(
            'size',
            'integer',
            'Size of the avatar',
            false,
        );
        $this->registerArgument(
            'fontSize',
            'float',
            'Font size of the avatar',
            false,
        );
        $this->registerArgument(
            'fontPath',
            'string',
            'Path to the font file',
            false,
        );
        $this->registerArgument(
            'foregroundColor',
            'string',
            'Foreground color of the avatar',
            false,
        );
        $this->registerArgument(
            'backgroundColor',
            'string',
            'Background color of the avatar',
            false,
        );
        $this->registerArgument(
            'mode',
            'string',
            'Color mode of the avatar (e.g. custom)',
            false,
        );
        $this->registerArgument(
            'theme',
            'string',
            'Theme of the avatar',
            false,
        );
        $this->registerArgument(
            'imageFormat',
            'string',
            'Image format of the avatar (e.g. png or jpeg)',
            false,
        );
        $this->registerArgument(
            'transform',
            'string',
            'Text transformation (e.g. uppercase)',
            false,
        );
        $this->registerArgument(
            'shape',
            'string',
            'Image shape (e.g. circle or square)',
            false,
        );
    }

    public function render(): string
    {
        if (($this->arguments['name'] ?? '') === '' && ($this->arguments['initials'] ?? '') === '') {
            throw new InvalidArgumentException('Either name or initials must be provided', 1204028706);
        }

        // Omit unresolved (null) values so the Avatar constructor defaults apply.
        $configuration = array_filter([
            'name' => $this->arguments['name'] ?? '',
            'initials' => $this->arguments['initials'] ?? '',
            'mode' => $this->resolveEnumArgument('mode', ColorMode::class, 'colorMode'),
            'theme' => $this->arguments['theme'] ?? ConfigurationUtility::get('theme'),
            'size' => $this->arguments['size'] ?? ConfigurationUtility::get('size'),
            'fontSize' => $this->arguments['fontSize'] ?? ConfigurationUtility::get('fontSize'),
            'fontPath' => $this->arguments['fontPath'] ?? ConfigurationUtility::get('fontPath'),
            'foregroundColor' => $this->arguments['foregroundColor'] ?? null,
            'backgroundColor' => $this->arguments['backgroundColor'] ?? null,
            'imageFormat' => $this->resolveEnumArgument('imageFormat', ImageFormat::class, 'imageFormat'),
            'transform' => $this->resolveEnumArgument('transform', Transform::class, 'transform'),
            'shape' => $this->resolveEnumArgument('shape', Shape::class, 'shape'),
        ], static fn (mixed $value): bool => null !== $value);

        $avatarService = Avatar::create(...$configuration);

        if (!file_exists($avatarService->getImagePath())) {
            $avatarService->save();
        }

        return $avatarService->getWebPath();
    }

    /**
     * @template T of BackedEnum
     *
     * @param class-string<T> $enumClass
     *
     * @return T|null
     */
    private function resolveEnumArgument(string $argument, string $enumClass, string $configurationKey): ?BackedEnum
    {
        $value = $this->arguments[$argument] ?? '';

        if (is_string($value) && '' !== $value) {
            return $enumClass::tryFrom($value) ?? throw new InvalidArgumentException(sprintf('Invalid value "%s" for avatar argument "%s".', $value, $argument), 1751719200);
        }

        $configured = ConfigurationUtility::get($configurationKey, $enumClass);

        return $configured instanceof $enumClass ? $configured : null;
    }
}

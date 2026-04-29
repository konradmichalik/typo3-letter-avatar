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

namespace KonradMichalik\Typo3LetterAvatar\AvatarProvider;

use InvalidArgumentException;
use KonradMichalik\Typo3LetterAvatar\Enum\{ColorMode, ImageFormat, Shape, Transform};
use KonradMichalik\Typo3LetterAvatar\Event\BackendUserAvatarConfigurationEvent;
use KonradMichalik\Typo3LetterAvatar\Image\Avatar;
use KonradMichalik\Typo3LetterAvatar\Utility\ConfigurationUtility;
use TYPO3\CMS\Backend\Backend\Avatar\{AvatarProviderInterface, Image};
use TYPO3\CMS\Core\EventDispatcher\EventDispatcher;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * LetterAvatarProvider.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class LetterAvatarProvider implements AvatarProviderInterface
{
    public function __construct(protected readonly EventDispatcher $eventDispatcher) {}

    public function getImage(array $backendUser, $size): ?Image
    {
        $mode = ConfigurationUtility::get('colorMode', ColorMode::class);
        if (null === $mode) {
            throw new InvalidArgumentException('Invalid color mode', 1204028706);
        }

        $configuration = [
            'name' => $this->getName($backendUser),
            'mode' => $mode,
            'theme' => (ColorMode::THEME === $mode) ? ConfigurationUtility::get('theme') : '',
            'size' => ConfigurationUtility::get('size'),
            'fontSize' => ConfigurationUtility::get('fontSize'),
            'fontPath' => ConfigurationUtility::get('fontPath'),
            'imageFormat' => ConfigurationUtility::get('imageFormat', ImageFormat::class),
            'transform' => ConfigurationUtility::get('transform', Transform::class),
            'shape' => ConfigurationUtility::get('shape', Shape::class),
        ];

        $this->eventDispatcher->dispatch(new BackendUserAvatarConfigurationEvent($backendUser, $configuration));
        $avatarService = Avatar::create(...$configuration);

        if (!file_exists($avatarService->getImagePath())) {
            $avatarService->save();
        }

        return GeneralUtility::makeInstance(
            Image::class,
            $avatarService->getWebPath(),
            ConfigurationUtility::get('size'),
            ConfigurationUtility::get('size'),
        );
    }

    private function getName(array $backendUser): string
    {
        if (ConfigurationUtility::get('prioritizeRealName')) {
            return $backendUser['realName'] ?? $backendUser['username'];
        }

        return $backendUser['username'];
    }
}

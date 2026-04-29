<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS extension "typo3_letter_avatar".
 *
 * Copyright (C) 2025-2026 Konrad Michalik <hej@konradmichalik.dev>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

namespace KonradMichalik\Typo3LetterAvatar\AvatarProvider;

use KonradMichalik\Typo3LetterAvatar\Enum\ColorMode;
use KonradMichalik\Typo3LetterAvatar\Enum\ImageFormat;
use KonradMichalik\Typo3LetterAvatar\Enum\Shape;
use KonradMichalik\Typo3LetterAvatar\Enum\Transform;
use KonradMichalik\Typo3LetterAvatar\Event\BackendUserAvatarConfigurationEvent;
use KonradMichalik\Typo3LetterAvatar\Image\Avatar;
use KonradMichalik\Typo3LetterAvatar\Utility\ConfigurationUtility;
use TYPO3\CMS\Backend\Backend\Avatar\AvatarProviderInterface;
use TYPO3\CMS\Backend\Backend\Avatar\Image;
use TYPO3\CMS\Core\EventDispatcher\EventDispatcher;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * LetterAvatarProvider.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0
 */
class LetterAvatarProvider implements AvatarProviderInterface
{
    public function __construct(protected readonly EventDispatcher $eventDispatcher) {}

    public function getImage(array $backendUser, $size): ?Image
    {
        $mode = ConfigurationUtility::get('colorMode', ColorMode::class);
        if ($mode === null) {
            throw new \InvalidArgumentException('Invalid color mode', 1204028706);
        }

        $configuration = [
            'name' => $this->getName($backendUser),
            'mode' => $mode,
            'theme' => ($mode === ColorMode::THEME) ? ConfigurationUtility::get('theme') : '',
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

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

namespace KonradMichalik\Typo3LetterAvatar\Tests\Unit\Event;

use KonradMichalik\Typo3LetterAvatar\Event\BackendUserAvatarConfigurationEvent;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * BackendUserAvatarConfigurationEventTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0
 */
final class BackendUserAvatarConfigurationEventTest extends TestCase
{
    #[Test]
    public function constructorSetsBackendUserAndConfiguration(): void
    {
        $backendUser = [
            'uid' => 1,
            'username' => 'admin',
            'realName' => 'Administrator',
        ];

        $configuration = [
            'size' => 100,
            'mode' => 'random',
            'theme' => 'colorful',
        ];

        $event = new BackendUserAvatarConfigurationEvent($backendUser, $configuration);

        self::assertSame($backendUser, $event->getBackendUser());
        self::assertSame($configuration, $event->getConfiguration());
    }

    #[Test]
    public function getBackendUserReturnsCorrectData(): void
    {
        $backendUser = [
            'uid' => 2,
            'username' => 'editor',
            'realName' => 'Content Editor',
            'admin' => 0,
        ];

        $event = new BackendUserAvatarConfigurationEvent($backendUser, []);

        $result = $event->getBackendUser();

        self::assertSame(2, $result['uid']);
        self::assertSame('editor', $result['username']);
        self::assertSame('Content Editor', $result['realName']);
        self::assertSame(0, $result['admin']);
    }

    #[Test]
    public function getConfigurationReturnsCorrectData(): void
    {
        $configuration = [
            'size' => 150,
            'fontSize' => 0.6,
            'foregroundColor' => '#FFFFFF',
            'backgroundColor' => '#000000',
        ];

        $event = new BackendUserAvatarConfigurationEvent([], $configuration);

        $result = $event->getConfiguration();

        self::assertSame(150, $result['size']);
        self::assertSame(0.6, $result['fontSize']);
        self::assertSame('#FFFFFF', $result['foregroundColor']);
        self::assertSame('#000000', $result['backgroundColor']);
    }

    #[Test]
    public function setConfigurationUpdatesConfiguration(): void
    {
        $initialConfig = ['size' => 100];
        $newConfig = [
            'size' => 200,
            'mode' => 'custom',
            'foregroundColor' => '#FF0000',
        ];

        $event = new BackendUserAvatarConfigurationEvent([], $initialConfig);

        // Verify initial state
        self::assertSame($initialConfig, $event->getConfiguration());

        // Update configuration
        $event->setConfiguration($newConfig);

        // Verify updated state
        self::assertSame($newConfig, $event->getConfiguration());
        self::assertNotSame($initialConfig, $event->getConfiguration());
    }

    #[Test]
    public function eventNameConstantIsCorrect(): void
    {
        self::assertSame(
            'typo3_letter_avatar.backend_user.modify_avatar_provider',
            BackendUserAvatarConfigurationEvent::NAME
        );
    }

    #[Test]
    public function configurationCanBeEmpty(): void
    {
        $event = new BackendUserAvatarConfigurationEvent([], []);

        self::assertSame([], $event->getConfiguration());
    }

    #[Test]
    public function backendUserCanBeEmpty(): void
    {
        $event = new BackendUserAvatarConfigurationEvent([], []);

        self::assertSame([], $event->getBackendUser());
    }

    #[Test]
    public function configurationCanBeOverwrittenCompletely(): void
    {
        $originalConfig = [
            'size' => 100,
            'mode' => 'random',
            'theme' => 'colorful',
            'foregroundColor' => '#FFFFFF',
        ];

        $newConfig = [
            'size' => 50,
            'mode' => 'custom',
        ];

        $event = new BackendUserAvatarConfigurationEvent([], $originalConfig);
        $event->setConfiguration($newConfig);

        $result = $event->getConfiguration();

        // New configuration should completely replace the old one
        self::assertSame(50, $result['size']);
        self::assertSame('custom', $result['mode']);
        self::assertArrayNotHasKey('theme', $result);
        self::assertArrayNotHasKey('foregroundColor', $result);
    }
}

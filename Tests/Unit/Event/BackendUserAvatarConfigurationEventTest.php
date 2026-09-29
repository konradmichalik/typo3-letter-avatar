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

namespace KonradMichalik\Typo3LetterAvatar\Tests\Unit\Event;

use KonradMichalik\Typo3LetterAvatar\Event\BackendUserAvatarConfigurationEvent;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * BackendUserAvatarConfigurationEventTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
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
}

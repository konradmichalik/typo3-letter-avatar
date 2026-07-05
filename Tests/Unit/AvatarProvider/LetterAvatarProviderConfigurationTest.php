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

namespace KonradMichalik\Typo3LetterAvatar\Tests\Unit\AvatarProvider;

use KonradMichalik\Typo3LetterAvatar\AvatarProvider\LetterAvatarProvider;
use KonradMichalik\Typo3LetterAvatar\Configuration;
use KonradMichalik\Typo3LetterAvatar\Enum\ColorMode;
use KonradMichalik\Typo3LetterAvatar\Event\BackendUserAvatarConfigurationEvent;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;
use ReflectionMethod;

/**
 * LetterAvatarProviderConfigurationTest.
 *
 * Verifies that the provider resolves the avatar configuration from the
 * extension settings and honours modifications made by event listeners.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
final class LetterAvatarProviderConfigurationTest extends TestCase
{
    protected function setUp(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][Configuration::EXT_KEY] = [
            'colorMode' => 'stringify',
            'theme' => 'colorful',
            'prioritizeRealName' => true,
        ];
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['configuration'] = [
            'size' => 50,
            'fontSize' => 0.5,
            'fontPath' => 'EXT:typo3_letter_avatar/Resources/Public/Fonts/OpenSans-Bold.ttf',
            'imageFormat' => 'png',
            'transform' => 'none',
            'shape' => 'circle',
        ];
    }

    protected function tearDown(): void
    {
        unset(
            $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][Configuration::EXT_KEY],
            $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY],
        );
    }

    #[Test]
    public function resolveConfigurationBuildsConfigurationFromExtensionSettings(): void
    {
        $configuration = $this->resolveConfiguration(
            $this->dispatcherWithoutListener(),
            ['username' => 'admin', 'realName' => 'Ada Lovelace'],
        );

        self::assertSame('Ada Lovelace', $configuration['name']);
        self::assertSame(ColorMode::STRINGIFY, $configuration['mode']);
        self::assertSame(50, $configuration['size']);
    }

    #[Test]
    public function resolveConfigurationAppliesListenerModifications(): void
    {
        $dispatcher = new class implements EventDispatcherInterface {
            public function dispatch(object $event): object
            {
                if ($event instanceof BackendUserAvatarConfigurationEvent) {
                    $configuration = $event->getConfiguration();
                    $configuration['mode'] = ColorMode::CUSTOM;
                    $configuration['foregroundColor'] = '#000000';
                    $configuration['backgroundColor'] = '#FFFFFF';
                    $event->setConfiguration($configuration);
                }

                return $event;
            }
        };

        $configuration = $this->resolveConfiguration($dispatcher, ['username' => 'admin']);

        self::assertSame(ColorMode::CUSTOM, $configuration['mode']);
        self::assertSame('#000000', $configuration['foregroundColor']);
        self::assertSame('#FFFFFF', $configuration['backgroundColor']);
    }

    private function dispatcherWithoutListener(): EventDispatcherInterface
    {
        return new class implements EventDispatcherInterface {
            public function dispatch(object $event): object
            {
                return $event;
            }
        };
    }

    /**
     * @param array<string, mixed> $backendUser
     *
     * @return array<string, mixed>
     */
    private function resolveConfiguration(EventDispatcherInterface $dispatcher, array $backendUser): array
    {
        $provider = new LetterAvatarProvider($dispatcher);
        $method = new ReflectionMethod($provider, 'resolveConfiguration');

        return $method->invoke($provider, $backendUser);
    }
}

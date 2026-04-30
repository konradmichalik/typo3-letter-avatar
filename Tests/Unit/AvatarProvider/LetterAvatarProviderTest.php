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
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use TYPO3\CMS\Backend\Backend\Avatar\AvatarProviderInterface;

/**
 * LetterAvatarProviderTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
final class LetterAvatarProviderTest extends TestCase
{
    #[Test]
    public function classImplementsAvatarProviderInterface(): void
    {
        $reflection = new ReflectionClass(LetterAvatarProvider::class);

        self::assertTrue($reflection->implementsInterface(AvatarProviderInterface::class));
    }

    #[Test]
    public function classDefinesGetImageMethod(): void
    {
        self::assertTrue(method_exists(LetterAvatarProvider::class, 'getImage'));
    }

    #[Test]
    public function classDefinesPrivateGetNameMethod(): void
    {
        $reflection = new ReflectionClass(LetterAvatarProvider::class);

        self::assertTrue($reflection->hasMethod('getName'));
        self::assertTrue($reflection->getMethod('getName')->isPrivate());
    }

    #[Test]
    public function constructorRequiresEventDispatcher(): void
    {
        $reflection = new ReflectionClass(LetterAvatarProvider::class);
        $constructor = $reflection->getConstructor();

        self::assertNotNull($constructor);
        $parameters = $constructor->getParameters();
        self::assertCount(1, $parameters);
        self::assertSame('eventDispatcher', $parameters[0]->getName());
    }

    #[Test]
    public function getImageMethodHasCorrectSignature(): void
    {
        $reflection = new ReflectionClass(LetterAvatarProvider::class);
        $method = $reflection->getMethod('getImage');

        $parameters = $method->getParameters();
        self::assertCount(2, $parameters);
        self::assertSame('backendUser', $parameters[0]->getName());
        self::assertSame('size', $parameters[1]->getName());
    }
}

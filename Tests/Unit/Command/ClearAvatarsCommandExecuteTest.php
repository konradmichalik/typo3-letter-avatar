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

namespace KonradMichalik\Typo3LetterAvatar\Tests\Unit\Command;

use KonradMichalik\Typo3LetterAvatar\Command\ClearAvatarsCommand;
use KonradMichalik\Typo3LetterAvatar\Configuration;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use TYPO3\CMS\Core\Core\{ApplicationContext, Environment};
use TYPO3\CMS\Core\Utility\GeneralUtility;

use function dirname;

/**
 * ClearAvatarsCommandExecuteTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
final class ClearAvatarsCommandExecuteTest extends TestCase
{
    private const IMAGE_PATH = '/.Build/var/tests/clear-avatars/';

    public static function setUpBeforeClass(): void
    {
        $extensionRoot = dirname(__DIR__, 3);
        Environment::initialize(
            new ApplicationContext('Testing'),
            true,
            false,
            $extensionRoot,
            $extensionRoot,
            $extensionRoot.'/.Build/var',
            $extensionRoot.'/.Build/var/config',
            $extensionRoot.'/.Build/public/index.php',
            'UNIX',
        );
    }

    protected function setUp(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['folderCreateMask'] = '2775';
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][Configuration::EXT_KEY] = [];
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['configuration'] = [
            'imagePath' => self::IMAGE_PATH,
        ];
    }

    protected function tearDown(): void
    {
        GeneralUtility::rmdir(Environment::getPublicPath().self::IMAGE_PATH, true);
        unset(
            $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][Configuration::EXT_KEY],
            $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY],
            $GLOBALS['TYPO3_CONF_VARS']['SYS']['folderCreateMask'],
        );
    }

    #[Test]
    public function dryRunCountsAvatarsWithoutDeletingThem(): void
    {
        $folder = $this->prepareFolderWithFiles();

        $tester = new CommandTester(new ClearAvatarsCommand());
        $exitCode = $tester->execute(['--dry-run' => true]);

        self::assertSame(Command::SUCCESS, $exitCode);
        self::assertStringContainsString('2 letter avatars would be cleared', $tester->getDisplay());
        self::assertFileExists($folder.'avatar-one.png');
        self::assertFileExists($folder.'avatar-two.jpeg');
    }

    #[Test]
    public function executeDeletesOnlyAvatarFiles(): void
    {
        $folder = $this->prepareFolderWithFiles();

        $tester = new CommandTester(new ClearAvatarsCommand());
        $exitCode = $tester->execute([]);

        self::assertSame(Command::SUCCESS, $exitCode);
        self::assertStringContainsString('2 letter avatars have been cleared', $tester->getDisplay());
        self::assertFileDoesNotExist($folder.'avatar-one.png');
        self::assertFileDoesNotExist($folder.'avatar-two.jpeg');
        // Non-avatar files must survive.
        self::assertFileExists($folder.'keep.txt');
        self::assertFileExists($folder.'.gitkeep');
    }

    #[Test]
    public function executeRefusesWhenImagePathResolvesToSiteRoot(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['configuration']['imagePath'] = '';

        $tester = new CommandTester(new ClearAvatarsCommand());
        $exitCode = $tester->execute([]);

        self::assertSame(Command::FAILURE, $exitCode);
        self::assertStringContainsString('Refusing to clear', $tester->getDisplay());
    }

    private function prepareFolderWithFiles(): string
    {
        $folder = Environment::getPublicPath().self::IMAGE_PATH;
        GeneralUtility::mkdir_deep($folder);

        file_put_contents($folder.'avatar-one.png', 'png');
        file_put_contents($folder.'avatar-two.jpeg', 'jpeg');
        file_put_contents($folder.'keep.txt', 'keep');
        file_put_contents($folder.'.gitkeep', '');

        return $folder;
    }
}

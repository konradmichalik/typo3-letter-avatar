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

namespace KonradMichalik\Typo3LetterAvatar\Command;

use KonradMichalik\Typo3LetterAvatar\Utility\PathUtility;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\{InputInterface, InputOption};
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

use function count;

/**
 * ClearAvatarsCommand.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
final class ClearAvatarsCommand extends Command
{
    protected function configure(): void
    {
        $this->setHelp('Clear all generated letter avatars.')
            ->addOption(
                'dry-run',
                null,
                InputOption::VALUE_NONE,
                'Simulate the deletion process without actually deleting files.',
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $path = PathUtility::getImageFolder();
        $output->writeln("🧽 - Clearing all generated letter avatars in <question>$path</question>");

        $imageCount = 0;
        if (is_dir($path)) {
            $files = scandir($path);
            $imageCount = count(array_filter($files, static fn (string $file): bool => is_file($path.\DIRECTORY_SEPARATOR.$file) && 1 === preg_match('/\.(png|jpg|jpeg)$/i', $file)));
        }

        if ((bool) $input->getOption('dry-run')) {
            $output->writeln("ℹ️ - <comment>$imageCount</comment> letter avatars would be cleared (dry-run).");

            return Command::SUCCESS;
        }

        $return = GeneralUtility::rmdir($path, true);

        if (false === $return) {
            $output->writeln('❌ - Failed to clear generated letter avatars.');

            return Command::FAILURE;
        }

        $output->writeln("✅  - <comment>$imageCount</comment> letter avatars have been cleared.");

        return Command::SUCCESS;
    }
}

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
use TYPO3\CMS\Core\Core\Environment;

use function count;
use function sprintf;

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

        if (!$this->isSafeAvatarPath($path)) {
            $output->writeln('❌ - Refusing to clear: the configured image path is invalid or points to the site root.');

            return Command::FAILURE;
        }

        $avatarFiles = $this->findAvatarFiles($path);

        if ((bool) $input->getOption('dry-run')) {
            $output->writeln(sprintf('ℹ️ - <comment>%d</comment> letter avatars would be cleared (dry-run).', count($avatarFiles)));

            return Command::SUCCESS;
        }

        $deleted = 0;
        foreach ($avatarFiles as $file) {
            if (@unlink($file)) {
                ++$deleted;
            }
        }

        if ($deleted !== count($avatarFiles)) {
            $output->writeln('❌ - Failed to clear generated letter avatars.');

            return Command::FAILURE;
        }

        $output->writeln(sprintf('✅  - <comment>%d</comment> letter avatars have been cleared.', $deleted));

        return Command::SUCCESS;
    }

    /**
     * Guards against a misconfigured image path (e.g. an empty imagePath
     * resolving to the site root) so recursive avatar deletion cannot touch
     * unrelated files outside a dedicated avatar directory.
     */
    private function isSafeAvatarPath(string $path): bool
    {
        $resolvedPath = realpath($path);
        $resolvedPublicPath = realpath(Environment::getPublicPath());

        return false !== $resolvedPath
            && false !== $resolvedPublicPath
            && $resolvedPath !== $resolvedPublicPath;
    }

    /**
     * @return list<string>
     */
    private function findAvatarFiles(string $path): array
    {
        if (!is_dir($path)) {
            return [];
        }

        $files = [];
        foreach (scandir($path) ?: [] as $entry) {
            $fullPath = $path.$entry;
            if (is_file($fullPath) && 1 === preg_match('/\.(png|jpe?g)$/i', $entry)) {
                $files[] = $fullPath;
            }
        }

        return $files;
    }
}

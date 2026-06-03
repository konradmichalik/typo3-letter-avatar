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

defined('TYPO3') || exit;

$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['backend']['avatarProviders']['letterAvatar'] = [
    'provider' => KonradMichalik\Typo3LetterAvatar\AvatarProvider\LetterAvatarProvider::class,
    'after' => ['defaultAvatarProvider'],
];

/*
* Default configuration
*/
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][KonradMichalik\Typo3LetterAvatar\Configuration::EXT_KEY]['configuration'] = [
    // Image size, in pixel
    'size' => 50,

    // Font size, in percentage
    'fontSize' => 0.5,

    // Font path
    // Attention: this entry will override the 'fontPath' configuration from the extension settings
    // 'fontPath' => 'EXT:typo3_letter_avatar/Resources/Public/Fonts/OpenSans-Bold.ttf',

    // Convert initial letter in uppercase, lowercase or keep as is
    'transform' => KonradMichalik\Typo3LetterAvatar\Enum\Transform::NONE,

    // Prioritize real name (or username) of a backend user for initial letters
    'prioritizeRealName' => true,

    // Default path for processed images
    'imagePath' => '/typo3temp/assets/avatars/',

    // Image format for processed images, png or jpeg
    'imageFormat' => KonradMichalik\Typo3LetterAvatar\Enum\ImageFormat::PNG,

    // Shape
    'shape' => KonradMichalik\Typo3LetterAvatar\Enum\Shape::CIRCLE,

    // Color mode
    // Attention: this entry will override the 'colorMode' configuration from the extension settings
    // 'colorMode' => \KonradMichalik\Typo3LetterAvatar\Enum\ColorMode::STRINGIFY->value,

    // Color mode: "Random"
    'random' => [
        // List of foreground colors to be used, randomly selected
        'foregrounds' => [
            '#FFFFFF',
        ],

        // List of background colors to be used, randomly selected
        'backgrounds' => [
            '#f44336',
            '#E91E63',
            '#9C27B0',
            '#673AB7',
            '#3F51B5',
            '#2196F3',
            '#03A9F4',
            '#00BCD4',
            '#009688',
            '#4CAF50',
            '#8BC34A',
            '#CDDC39',
            '#FFC107',
            '#FF9800',
            '#FF5722',
        ],
    ],

    // Color mode: "Pairs"
    'pairs' => [
        [
            'background' => '#626F47',
            'foreground' => '#F0BB78',
        ],
        [
            'background' => '#FE5D26',
            'foreground' => '#C1DBB3',
        ],
        [
            'background' => '#533B4D',
            'foreground' => '#FAE3C6',
        ],
        [
            'background' => '#5409DA',
            'foreground' => '#8DD8FF',
        ],
        [
            'background' => '#096B68',
            'foreground' => '#FFFBDE',
        ],
        [
            'background' => '#2A4759',
            'foreground' => '#F79B72',
        ],
        [
            'background' => '#213448',
            'foreground' => '#ECEFCA',
        ],
        [
            'background' => '#626F47',
            'foreground' => '#F0BB78',
        ],
        [
            'background' => '#183B4E',
            'foreground' => '#DDA853',
        ],
        [
            'background' => '#A86523',
            'foreground' => '#FCEFCB',
        ],
        [
            'background' => '#B6B09F',
            'foreground' => '#F2F2F2',
        ],
        [
            'background' => '#4B352A',
            'foreground' => '#B2CD9C',
        ],
        [
            'background' => '#333446',
            'foreground' => '#B8CFCE',
        ],
        [
            'background' => '#537D5D',
            'foreground' => '#D2D0A0',
        ],
        [
            'background' => '#393E46',
            'foreground' => '#DFD0B8',
        ],
    ],

    // Theme selection
    // Attention: this entry will override the 'theme' configuration from the extension settings
    // 'theme' => 'grayscale-light',

    // Color mode: "Theme"
    'themes' => [
        'grayscale-light' => [
            'backgrounds' => ['#edf2f7', '#e2e8f0', '#cbd5e0'],
            'foregrounds' => ['#a0aec0'],
        ],
        'grayscale-dark' => [
            'backgrounds' => ['#2d3748', '#4a5568', '#718096'],
            'foregrounds' => ['#e2e8f0'],
        ],
        'colorful' => [
            'backgrounds' => [
                '#f44336',
                '#E91E63',
                '#9C27B0',
                '#673AB7',
                '#3F51B5',
                '#2196F3',
                '#03A9F4',
                '#00BCD4',
                '#009688',
                '#4CAF50',
                '#8BC34A',
                '#CDDC39',
                '#FFC107',
                '#FF9800',
                '#FF5722',
            ],
            'foregrounds' => [
                '#FFFFFF',
            ],
        ],
        'pastel' => [
            'backgrounds' => [
                '#ef9a9a',
                '#F48FB1',
                '#CE93D8',
                '#B39DDB',
                '#9FA8DA',
                '#90CAF9',
                '#81D4FA',
                '#80DEEA',
                '#80CBC4',
                '#A5D6A7',
                '#E6EE9C',
                '#FFAB91',
                '#FFCCBC',
                '#D7CCC8',
            ],
            'foregrounds' => [
                '#FFFFFF',
            ],
        ],
        'typo3' => [
            'backgrounds' => ['#FF8700'],
            'foregrounds' => ['#000000'],
        ],
        // Palettes mirroring the TYPO3 v14 backend "Theme" accents.
        // Values extracted from cms-backend/Resources/Public/Css/backend.css.
        'backend-modern' => [
            'backgrounds' => ['#205eb5'], // --token-color-primary-base (default theme)
            'foregrounds' => ['#FFFFFF'],
        ],
        'backend-fresh' => [
            'backgrounds' => ['#5033c7'], // [data-theme=fresh] --token-color-primary-base
            'foregrounds' => ['#FCFCFE'],
        ],
        'backend-classic' => [
            'backgrounds' => ['#ff8700'], // [data-theme=classic] --typo3-icons-accent
            'foregrounds' => ['#3D3D3D'],
        ],
    ],

    // Color mode: "Backend Theme"
    // Resolves a palette based on the currently logged-in backend user's theme settings,
    // so all rendered avatars match the viewer's UI. Lookup priority:
    //   1. "<colorScheme>:<theme>" (e.g. "dark:fresh") — explicit per-combination override
    //   2. "<theme>"               (e.g. "fresh")     — primary axis
    //   3. "<colorScheme>"         (e.g. "dark")     — fallback when theme is unknown
    //   4. "default"
    // Each value must reference a palette key from 'themes' above.
    'backendThemes' => [
        // Theme-specific palettes (primary axis).
        'modern' => 'backend-modern',
        'fresh' => 'backend-fresh',
        'classic' => 'backend-classic',
        // Color-scheme safety net (used only if TYPO3 ever adds an unknown theme value).
        'light' => 'grayscale-light',
        'dark' => 'grayscale-dark',
        'auto' => 'grayscale-light',
        'default' => 'backend-modern',
    ],
];

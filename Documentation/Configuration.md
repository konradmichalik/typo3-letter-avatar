# Configuration

> **Note:** Examples below apply to TYPO3 v13.4 LTS and v14.x. For v11/v12 compatibility, use the 1.x line of this extension.

## Extension Configuration

Configure the extension via `Admin Tools > Settings > Extension Configuration > typo3_letter_avatar`:

### Color Mode
- **STRINGIFY**: Random color based on name
- **RANDOM**: Randomly selected colors from predefined list
- **THEME**: Predefined color theme
- **PAIRS**: Randomly selected color pairs
- **CUSTOM**: Custom colors (code configuration only)

### Theme
Select color theme when using "Theme" mode. Available themes defined in `ext_localconf.php`.

### Font
Choose from various included font types for avatar generation.

## Custom Configuration

Override default configuration in your extension:

```php
// Add custom theme
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['typo3_letter_avatar']['configuration']['themes']['customTheme'] = [
    'foregrounds' => [
        '#FFFFFF',
        '#000000',
        '#333333',
        '#FFFAFA',
        '#F5F5F5',
    ],
    'backgrounds' => [
        '#1E90FF',
        '#32CD32',
        '#FF4500',
        '#FFD700',
        '#8A2BE2',
    ],
];

// Set custom theme (overrides extension setting)
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['typo3_letter_avatar']['configuration']['theme'] = 'customTheme';
```

## Available Options

See `ext_localconf.php` for complete configuration structure:
- Color modes and themes (line 44+)
- Color pairs (line 71+) 
- Predefined themes (line 107+)
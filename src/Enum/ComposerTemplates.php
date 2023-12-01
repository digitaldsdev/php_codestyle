<?php

declare(strict_types=1);

namespace DigitalSector\CodeStyle\Enum;

final class ComposerTemplates
{
    public const EXTRA_MAIN = [
        'hooks' => [
            'config' => [
                'stop-on-failure' => ["pre-push"],
            ],
            'pre-commit' => [
                'echo codestyle check',
            ],
        ],
    ];

    public const SCRIPTS = [
        'post-install-cmd' => ['cghooks add'],
        'post-update-cmd' => ['cghooks update'],
        Commands::CODE_STYLE_FIX => 'vendor/bin/php-cs-fixer fix --path-mode=intersection --config vendor/digital-sector/codestyle/.php_cs-fixer.php --allow-risky=yes',
        Commands::CODE_STYLE_CHECK => 'vendor/bin/php-cs-fixer fix --path-mode=intersection --config vendor/digital-sector/codestyle/.php_cs-fixer.php --dry-run --allow-risky=yes',
        Commands::CODE_ANALYZE => 'vendor/bin/phpstan analyse -c phpstan.neon --ansi',
    ];
}

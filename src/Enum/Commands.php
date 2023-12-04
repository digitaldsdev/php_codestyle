<?php

declare(strict_types=1);

namespace DigitalSector\CodeStyle\Enum;

final class Commands
{
    public const POST_INSTALL_CMD_NAME = 'post-install-cmd';
    public const POST_UPDATE_CMD_NAME = 'post-update-cmd';
    public const CODE_STYLE_PHPLINT_NAME = 'code-style:phplint';
    public const CODE_STYLE_FIX_NAME = 'code-style:fix';
    public const CODE_STYLE_CHECK_NAME = 'code-style:check';
    public const CODE_STYLE_ANALYZE_NAME = 'code-style:analyze';

    public const POST_INSTALL_CMD = ['cghooks add'];
    public const POST_UPDATE_CMD = ['cghooks update'];
    public const CODE_STYLE_PHPLINT = 'php -l -f';
    public const CODE_STYLE_FIX = 'vendor/bin/php-cs-fixer fix --path-mode=intersection --config vendor/digital-sector/codestyle/.php_cs-fixer.php --allow-risky=yes';
    public const CODE_STYLE_CHECK = 'vendor/bin/php-cs-fixer fix --path-mode=intersection --config vendor/digital-sector/codestyle/.php_cs-fixer.php --dry-run --allow-risky=yes';
    public const CODE_STYLE_ANALYZE = 'vendor/bin/phpstan analyse -c phpstan.neon --ansi';
}

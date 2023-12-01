<?php

declare(strict_types=1);

namespace DigitalSector\CodeStyle\Enum;

final class Commands
{
    public const POST_INSTALL_CMD = 'post-install-cmd';
    public const POST_UPDATE_CMD = 'post-update-cmd';
    public const CODE_STYLE_FIX = 'code-style:fix';
    public const CODE_STYLE_CHECK = 'code-style:check';
    public const CODE_STYLE_ANALYZE = 'code-style:analyze';
}

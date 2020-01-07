<?php

/**
 * Copyright (c) 2017-2020 gyselroth™  (http://www.gyselroth.net)
 *
 * @package \gyselroth\Helper
 * @author  gyselroth™  (http://www.gyselroth.com)
 * @link    http://www.gyselroth.com
 * @license Apache-2.0
 */

namespace Gyselroth\Helper\Interfaces;

interface ConstantsCliStylesInterface
{
    // CLI output colors
    public const CLI_STYLE_DEFAULT_COLORS = "\033[0m";

    public const CLI_STYLE_BLUE        = "\033[0;34m";
    public const CLI_STYLE_DARK_GRAY   = "\033[1;30m";
    public const CLI_STYLE_GREEN       = "\033[0;32m";
    public const CLI_STYLE_GRAY        = "\033[0;37m";
    public const CLI_STYLE_LIGHT_GREEN = "\033[1;32m";
    public const CLI_STYLE_LIGHT_RED   = "\033[1;31m";
    public const CLI_STYLE_RED         = "\033[0;31m";
    public const CLI_STYLE_OLIVE       = "\033[0;33m";
    public const CLI_STYLE_PINK        = "\033[0;35m";
    public const CLI_STYLE_WHITE       = "\033[1;37m";

    public const CLI_STYLE_BLACK_ON_WHITE = "\e[7;40m\e[37m";

    public const CLI_STYLE_BLINK     = "\033[5;31m";
    public const CLI_STYLE_BOLD      = "\033[1;31m";
    public const CLI_STYLE_NORMAL    = "\033[0;31m";
    public const CLI_STYLE_UNDERLINE = "\033[4;31m";
}

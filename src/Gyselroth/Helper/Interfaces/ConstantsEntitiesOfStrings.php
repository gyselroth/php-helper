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

interface ConstantsEntitiesOfStrings
{
    public const CHARSET_UTF8 = 'UTF-8';

    // Character classes
    public const CHAR_TYPE_ALPHA_LOWER = 0;
    public const CHAR_TYPE_ALPHA_UPPER = 1;
    public const CHAR_TYPE_NUMBER      = 2;
    public const CHAR_TYPE_SPECIAL     = 3;

    public CONST UMLAUTS = ['ä', 'ö', 'ü', 'Ä', 'Ö', 'Ü', 'ß'];
}

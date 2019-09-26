<?php

/**
 * Copyright (c) 2017-2019 gyselroth™  (http://www.gyselroth.net)
 *
 * @package \gyselroth\Helper
 * @author  gyselroth™  (http://www.gyselroth.com)
 * @link    http://www.gyselroth.com
 * @license Apache-2.0
 */

namespace Gyselroth\Helper\Interfaces;

interface ConstantsDataTypesInterface
{
    public const DATA_TYPE_ARRAY            = 'array';
    public const DATA_TYPE_ARRAY_OF_INTS    = 'array.int';
    public const DATA_TYPE_ARRAY_OF_STRINGS = 'array.string';
    public const DATA_TYPE_BOOL             = 'bool';
    public const DATA_TYPE_FLOAT            = 'float';
    public const DATA_TYPE_INT              = 'integer';
    public const DATA_TYPE_INT_SHORT        = 'int';
    public const DATA_TYPE_OBJECT           = 'object';
    public const DATA_TYPE_STRING           = 'string';

    public const TYPE_ID_OBJECT = 0;
    public const TYPE_ID_ARRAY = 1;
}

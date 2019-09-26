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

interface ConstantsUnitsOfDataMeasurementInterface
{
    // Byte sizes
    public const BYTES_KILOBYTE    = 1024;
    // 1 MB = 1024 * 1024 bytes
    public const BYTES_MEGABYTE    = 1048576;
    // 1 GB = 1024 * 1024 * 1024 bytes
    public const BYTES_GIGABYTE    = 1073741824;

    public const UNIT_BYTES     = 'B';
    public const UNIT_KILOBYTES = 'KB';
    public const UNIT_MEGABYTES = 'MB';
}

<?php

/**
 * Copyright (c) 2017-2019 gyselroth™  (http://www.gyselroth.net)
 *
 * @package \gyselroth\Helper
 * @author  gyselroth™  (http://www.gyselroth.com)
 * @link    http://www.gyselroth.com
 * @license Apache-2.0
 */

namespace Tests;

use Gyselroth\Helper\Interfaces\ConstantsDataTypesInterface;

class ConstantsDataTypesInterfaceTest extends HelperTestCase implements ConstantsDataTypesInterface
{
    public function testAutoload(): void
    {
        $this->assertSame('array', self::DATA_TYPE_ARRAY);
    }
}

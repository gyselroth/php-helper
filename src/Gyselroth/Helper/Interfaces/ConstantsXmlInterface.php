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

interface ConstantsXmlInterface
{
    // DOM classes
    public const DOM_CLASS_ELEMENT   = 'DOMElement';
    public const DOM_CLASS_NODE_LIST = 'DOMNodeList';

    // XML tag types
    public const TAG_TYPE_CLOSE    = 'close';
    public const TAG_TYPE_COMPLETE = 'complete';
    public const TAG_TYPE_OPEN     = 'open';

    public const ENCODING_UTF_8 = 'UTF-8';
    public const VERSION_1_0    = '1.0';
}

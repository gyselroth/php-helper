<?php

/**
 * Copyright (c) 2017-2019 gyselroth™  (http://www.gyselroth.net)
 *
 * @package \gyselroth\Helper
 * @author  gyselroth™  (http://www.gyselroth.com)
 * @link    http://www.gyselroth.com
 * @license Apache-2.0
 */

namespace Tests\Fixtures;

class CustomLogMock
{
	private $_pathToLogfile;

	public function __construct(string $pathToLogfile)
	{
		$this->_pathToLogfile = $pathToLogfile;
	}

    public function log(string $message, int $priority, array $options)
    {
    	$message .= " [priority: $priority]" . (!empty($options) ? ' [options: ' . implode(', ', $options) . ']' : '');
    	file_put_contents($this->_pathToLogfile, $message, FILE_APPEND);
    }
}

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

use \Monolog\Logger;
use \Monolog\Handler\HandlerInterface;

class MonologMock extends Logger
{
    /** @var string */
    private $_pathToLogfile;

    /** @var bool */
    private $_hasStreamHandlers;

    /**
     * Constructor
     *
     * @param string $pathToLogfile
     * @param bool   $hasStreamHandlers
     */
    public function __construct(string $pathToLogfile, bool $hasStreamHandlers = true)
    {
        $this->_pathToLogfile     = $pathToLogfile;
        $this->_hasStreamHandlers = $hasStreamHandlers;
    }

    /**
     * @return bool
     */
    public function getHandlers(): bool
    {
        return $this->_hasStreamHandlers;
    }

    /**
     * @param  HandlerInterface $handler
     * @return void
     */
    public function pushHandler(HandlerInterface $handler): void
    {
        $this->_hasStreamHandlers = true;
    }

    /**
     * @param  int|string $priority
     * @param  string     $message
     * @param  array      $options
     * @return void
     */
    public function log($priority, $message, array $options = []): void
    {
        $message .= " [priority: $priority]" . (!empty($options) ? ' [options: ' . implode(', ', $options) . ']' : '');
        /** @noinspection ReturnFalseInspection */
        file_put_contents($this->_pathToLogfile, $message, FILE_APPEND);
    }
}

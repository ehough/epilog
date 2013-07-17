<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Stores to PHP error_log() handler.
 *
 * @author Elan Ruusamäe <glen@delfi.ee>
 */
class ehough_epilog_handler_ErrorLogHandler extends ehough_epilog_handler_AbstractProcessingHandler
{
    protected $messageType;

    /**
     * @param integer $messageType  Says where the error should go.
     * @param integer $level  The minimum logging level at which this handler will be triggered
     * @param Boolean $bubble Whether the messages that are handled can bubble up the stack or not
     */
    public function __construct($messageType = 0, $level = ehough_epilog_Logger::DEBUG, $bubble = true)
    {
        parent::__construct($level, $bubble);
        if (!in_array($messageType, array(0, 4))) {
            throw new InvalidArgumentException('Only message types 0 and 4 are supported');
        }
        $this->messageType = $messageType;
    }

    /**
     * {@inheritdoc}
     */
    protected function write(array $record)
    {
        error_log((string) $record['formatted'], $this->messageType);
    }
}

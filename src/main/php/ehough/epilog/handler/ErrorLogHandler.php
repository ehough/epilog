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
    const OPERATING_SYSTEM = 0;
    const SAPI = 4;

    protected $messageType;

    private $_writer;

    /**
     * @param integer $messageType Says where the error should go.
     * @param integer $level       The minimum logging level at which this handler will be triggered
     * @param Boolean $bubble      Whether the messages that are handled can bubble up the stack or not
     */
    public function __construct($messageType = self::OPERATING_SYSTEM, $level = ehough_epilog_Logger::DEBUG, $bubble = true)
    {
        parent::__construct($level, $bubble);

        if (false === in_array($messageType, self::getAvailableTypes())) {
            $message = sprintf('The given message type "%s" is not supported', print_r($messageType, true));
            throw new InvalidArgumentException($message);
        }

        $this->messageType = $messageType;
    }

    /**
     * @return array With all available types
     */
    public static function getAvailableTypes()
    {
        return array(
            self::OPERATING_SYSTEM,
            self::SAPI,
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function write(array $record)
    {
        if (isset($this->_writer)) {

            call_user_func($this->_writer, (string) $record['formatted'], $this->messageType);

        } else {

            error_log((string) $record['formatted'], $this->messageType);
        }
    }

    /**
     * This method should never be used outside of testing!
     *
     * @param $callback
     */
    public function __setWriter($callback)
    {
        $this->_writer = $callback;
    }
}

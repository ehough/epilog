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
 * Stores to any stream resource
 *
 * Can be used to store into php://stderr, remote and local files, etc.
 *
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
class ehough_epilog_handler_StreamHandler extends ehough_epilog_handler_AbstractProcessingHandler
{
    protected $stream;
    protected $url;

    private $_errorMessage;

    /**
     * @param string  $stream
     * @param integer $level  The minimum logging level at which this handler will be triggered
     * @param Boolean $bubble Whether the messages that are handled can bubble up the stack or not
     */
    public function __construct($stream, $level = ehough_epilog_Logger::DEBUG, $bubble = true)
    {
        parent::__construct($level, $bubble);
        if (is_resource($stream)) {
            $this->stream = $stream;
        } else {
            $this->url = $stream;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        if (is_resource($this->stream)) {
            fclose($this->stream);
        }
        $this->stream = null;
    }

    /**
     * {@inheritdoc}
     */
    protected function write(array $record)
    {
        if (null === $this->stream) {
            if (!$this->url) {
                throw new LogicException('Missing stream url, the stream can not be opened. This may be caused by a premature call to close().');
            }
            $this->_errorMessage = null;
            set_error_handler(array($this, '_callbackWriteErrorHandler'));
            $this->stream = fopen($this->url, 'a');
            restore_error_handler();
            if (!is_resource($this->stream)) {
                $this->stream = null;
                throw new UnexpectedValueException(sprintf('The stream or file "%s" could not be opened: '.$this->_errorMessage, $this->url));
            }
        }
        fwrite($this->stream, (string) $record['formatted']);
    }

    public function _callbackWriteErrorHandler($code, $msg)
    {
        $this->_errorMessage = preg_replace('{^fopen\(.*?\): }', '', $msg);
    }
}

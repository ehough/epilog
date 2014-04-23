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
 * @author Robert Kaufmann III <rok3@rok3.me>
 */
class ehough_epilog_handler_LogEntriesHandler extends ehough_epilog_handler_SocketHandler
{

    /**
     * @var string
     */
    protected $logToken;

    /**
     * @param string  $token  Log token supplied by LogEntries
     * @param boolean $useSSL Whether or not SSL encryption should be used.
     * @param int     $level  The minimum logging level to trigger this handler
     * @param boolean $bubble Whether or not messages that are handled should bubble up the stack.
     *
     * @throws ehough_epilog_handler_MissingExtensionException If SSL encryption is set to true and OpenSSL is missing
     */
    public function __construct($token, $useSSL = true, $level = ehough_epilog_Logger::DEBUG, $bubble = true)
    {
        if ($useSSL && !extension_loaded('openssl')) {
            throw new ehough_epilog_handler_MissingExtensionException('The OpenSSL PHP plugin is required to use SSL encrypted connection for LogEntriesHandler');
        }

        $endpoint = $useSSL ? 'ssl://api.logentries.com:20000' : 'data.logentries.com:80';
        parent::__construct($endpoint, $level, $bubble);
        $this->logToken = $token;
    }

    /**
     * {@inheritdoc}
     *
     * @param  array  $record
     * @return string
     */
    protected function generateDataStream($record)
    {
        return $this->logToken . ' ' . $record['formatted'];
    }

}
<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//namespace Monolog;

//use ehough_epilog_handler_TestHandler;
//use Monolog\Formatter\LineFormatter;
//use Monolog\Processor\PsrLogMessageProcessor;
//use Psr\Log\Test\LoggerInterfaceTest;

class ehough_epilog_PsrLogCompatTest extends ehough_epilog_psr_test_LoggerInterfaceTest
{
    private $handler;

    public function getLogger()
    {
        $logger = new ehough_epilog_Logger('foo');
        $logger->pushHandler($handler = new ehough_epilog_handler_TestHandler);
        $logger->pushProcessor(new ehough_epilog_processor_PsrLogMessageProcessor);
        $handler->setFormatter(new ehough_epilog_formatter_LineFormatter('%level_name% %message%'));

        $this->handler = $handler;

        return $logger;
    }

    public function getLogs()
    {
        $convert = array($this, '_callbackGetLogs1');

        return array_map($convert, $this->handler->getRecords());
    }

    public function _callbackGetLogs1($record)
    {
        $lower = array($this, '_callbackGetLogs2');

        return preg_replace_callback('{^[A-Z]+}', $lower, $record['formatted']);
    }

    public function _callbackGetLogs2($match)
    {
        return strtolower($match[0]);
    }
}

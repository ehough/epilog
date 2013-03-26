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

class ehough_epilog_TestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @return array Record
     */
    protected function getRecord($level = ehough_epilog_Logger::WARNING, $message = 'test', $context = array())
    {
        return array(
            'message' => $message,
            'context' => $context,
            'level' => $level,
            'level_name' => ehough_epilog_Logger::getLevelName($level),
            'channel' => 'test',
            'datetime' => $this->_createDateTimeFromFormat(),
            'extra' => array(),
        );
    }

    /**
     * @return array
     */
    protected function getMultipleRecords()
    {
        return array(
            $this->getRecord(ehough_epilog_Logger::DEBUG, 'debug message 1'),
            $this->getRecord(ehough_epilog_Logger::DEBUG, 'debug message 2'),
            $this->getRecord(ehough_epilog_Logger::INFO, 'information'),
            $this->getRecord(ehough_epilog_Logger::WARNING, 'warning'),
            $this->getRecord(ehough_epilog_Logger::ERROR, 'error')
        );
    }

    /**
     * @return ehough_epilog_formatter_FormatterInterface
     */
    protected function getIdentityFormatter()
    {
        $formatter = $this->getMock('ehough_epilog_formatter_FormatterInterface');
        $formatter->expects($this->any())
            ->method('format')
            ->will($this->returnCallback(array($this, '_callbackGetIdentityFormatter')));

        return $formatter;
    }

    public function _callbackGetIdentityFormatter($record)
    {
        return $record['message'];
    }

    private function _createDateTimeFromFormat()
    {
        if (version_compare(PHP_VERSION, '5.3') >= 0) {

            return DateTime::createFromFormat('U.u', sprintf('%.6F', microtime(true)));
        }

        $time = new DateTime('@' . time());

        return $time;
    }
}

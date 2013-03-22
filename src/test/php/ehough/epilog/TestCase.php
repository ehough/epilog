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

class ehough_epilog_TestCase extends \PHPUnit_Framework_TestCase
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
            'datetime' => \DateTime::createFromFormat('U.u', sprintf('%.6F', microtime(true))),
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
            ->will($this->returnCallback(function($record) { return $record['message']; }));

        return $formatter;
    }
}

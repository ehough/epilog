<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class ChromePHPFormatterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ehough_epilog_formatter_ChromePHPFormatter::format
     */
    public function testDefaultFormat()
    {
        $formatter = new ehough_epilog_formatter_ChromePHPFormatter();
        $record = array(
            'level' => ehough_epilog_Logger::ERROR,
            'level_name' => 'ERROR',
            'channel' => 'meh',
            'context' => array('from' => 'logger'),
            'datetime' => new DateTime("@0"),
            'extra' => array('ip' => '127.0.0.1'),
            'message' => 'log',
        );

        $message = $formatter->format($record);

        $this->assertEquals(
            array(
                'meh',
                array(
                    'message' => 'log',
                    'context' => array('from' => 'logger'),
                    'extra' => array('ip' => '127.0.0.1'),
                ),
                'unknown',
                'error'
            ),
            $message
        );
    }

    /**
     * @covers ehough_epilog_formatter_ChromePHPFormatter::format
     */
    public function testFormatWithFileAndLine()
    {
        $formatter = new ehough_epilog_formatter_ChromePHPFormatter();
        $record = array(
            'level' => ehough_epilog_Logger::CRITICAL,
            'level_name' => 'CRITICAL',
            'channel' => 'meh',
            'context' => array('from' => 'logger'),
            'datetime' => new DateTime("@0"),
            'extra' => array('ip' => '127.0.0.1', 'file' => 'test', 'line' => 14),
            'message' => 'log',
        );

        $message = $formatter->format($record);

        $this->assertEquals(
            array(
                'meh',
                array(
                    'message' => 'log',
                    'context' => array('from' => 'logger'),
                    'extra' => array('ip' => '127.0.0.1'),
                ),
                'test : 14',
                'error'
            ),
            $message
        );
    }

    /**
     * @covers ehough_epilog_formatter_ChromePHPFormatter::format
     */
    public function testFormatWithoutContext()
    {
        $formatter = new ehough_epilog_formatter_ChromePHPFormatter();
        $record = array(
            'level' => ehough_epilog_Logger::DEBUG,
            'level_name' => 'DEBUG',
            'channel' => 'meh',
            'context' => array(),
            'datetime' => new DateTime("@0"),
            'extra' => array(),
            'message' => 'log',
        );

        $message = $formatter->format($record);

        $this->assertEquals(
            array(
                'meh',
                'log',
                'unknown',
                'log'
            ),
            $message
        );
    }

    /**
     * @covers ehough_epilog_formatter_ChromePHPFormatter::formatBatch
     */
    public function testBatchFormatThrowException()
    {
        $formatter = new ehough_epilog_formatter_ChromePHPFormatter();
        $records = array(
            array(
                'level' => ehough_epilog_Logger::INFO,
                'level_name' => 'INFO',
                'channel' => 'meh',
                'context' => array(),
                'datetime' => new DateTime("@0"),
                'extra' => array(),
                'message' => 'log',
            ),
            array(
                'level' => ehough_epilog_Logger::WARNING,
                'level_name' => 'WARNING',
                'channel' => 'foo',
                'context' => array(),
                'datetime' => new DateTime("@0"),
                'extra' => array(),
                'message' => 'log2',
            ),
        );

        $this->assertEquals(
            array(
                array(
                    'meh',
                    'log',
                    'unknown',
                    'info'
                ),
                array(
                    'foo',
                    'log2',
                    'unknown',
                    'warn'
                ),
            ),
            $formatter->formatBatch($records)
        );
    }
}

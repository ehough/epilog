<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//namespace Monolog\Formatter;

//use Monolog\Logger;
//use Monolog\TestCase;

class ehough_epilog_formatter_JsonFormatterTest extends ehough_epilog_TestCase
{
    /**
     * @covers Monolog\Formatter\JsonFormatter::format
     */
    public function testFormat()
    {
        $formatter = new ehough_epilog_formatter_JsonFormatter();
        $record = $this->getRecord();
        $this->assertEquals(json_encode($record), $formatter->format($record));
    }

    /**
     * @covers Monolog\Formatter\JsonFormatter::formatBatch
     */
    public function testFormatBatch()
    {
        $formatter = new ehough_epilog_formatter_JsonFormatter();
        $records = array(
            $this->getRecord(ehough_epilog_Logger::WARNING),
            $this->getRecord(ehough_epilog_Logger::DEBUG),
        );
        $this->assertEquals(json_encode($records), $formatter->formatBatch($records));
    }
}

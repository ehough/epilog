<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class LogglyFormatterTest extends ehough_epilog_TestCase
{
    /**
     * @covers ehough_epilog_formatter_LogglyFormatter::__construct
     */
    public function testConstruct()
    {
        $formatter = new ehough_epilog_formatter_LogglyFormatter();
        $this->assertEquals(ehough_epilog_formatter_LogglyFormatter::BATCH_MODE_NEWLINES, $formatter->getBatchMode());
        $formatter = new ehough_epilog_formatter_LogglyFormatter(ehough_epilog_formatter_LogglyFormatter::BATCH_MODE_JSON);
        $this->assertEquals(ehough_epilog_formatter_LogglyFormatter::BATCH_MODE_JSON, $formatter->getBatchMode());
    }

    /**
     * @covers Monolog\Formatter\LogglyFormatter::format
     */
    public function testFormat()
    {
        $formatter = new ehough_epilog_formatter_LogglyFormatter();
        $record = $this->getRecord();
        $formatted_decoded = json_decode($formatter->format($record), true);
        $this->assertArrayHasKey("timestamp", $formatted_decoded);
        $this->assertEquals(new DateTime($formatted_decoded["timestamp"]), $record["datetime"]);
    }
}

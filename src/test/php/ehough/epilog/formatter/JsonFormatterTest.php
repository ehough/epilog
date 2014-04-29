<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class ehough_epilog_formatter_JsonFormatterTest extends ehough_epilog_TestCase
{
    /**
     * @covers ehough_epilog_formatter_JsonFormatter::__construct
     * @covers ehough_epilog_formatter_JsonFormatter::getBatchMode
     * @covers ehough_epilog_formatter_JsonFormatter::isAppendingNewlines
     */
    public function testConstruct()
    {
        $formatter = new ehough_epilog_formatter_JsonFormatter();
        $this->assertEquals(ehough_epilog_formatter_JsonFormatter::BATCH_MODE_JSON, $formatter->getBatchMode());
        $this->assertEquals(true, $formatter->isAppendingNewlines());
        $formatter = new ehough_epilog_formatter_JsonFormatter(ehough_epilog_formatter_JsonFormatter::BATCH_MODE_NEWLINES, false);
        $this->assertEquals(ehough_epilog_formatter_JsonFormatter::BATCH_MODE_NEWLINES, $formatter->getBatchMode());
        $this->assertEquals(false, $formatter->isAppendingNewlines());
    }

    /**
     * @covers ehough_epilog_formatter_JsonFormatter::format
     */
    public function testFormat()
    {
        $formatter = new ehough_epilog_formatter_JsonFormatter();
        $record = $this->getRecord();
        $this->assertEquals(json_encode($record)."\n", $formatter->format($record));

        $formatter = new ehough_epilog_formatter_JsonFormatter(ehough_epilog_formatter_JsonFormatter::BATCH_MODE_JSON, false);
        $record = $this->getRecord();
        $this->assertEquals(json_encode($record), $formatter->format($record));
    }

    /**
     * @covers ehough_epilog_formatter_JsonFormatter::formatBatch
     * @covers ehough_epilog_formatter_JsonFormatter::formatBatchJson
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

    /**
     * @covers ehough_epilog_formatter_JsonFormatter::formatBatch
     * @covers ehough_epilog_formatter_JsonFormatter::formatBatchNewlines
     */
    public function testFormatBatchNewlines()
    {

        $formatter = new ehough_epilog_formatter_JsonFormatter(ehough_epilog_formatter_JsonFormatter::BATCH_MODE_NEWLINES);
        $records = $expected = array(
            $this->getRecord(ehough_epilog_Logger::WARNING),
            $this->getRecord(ehough_epilog_Logger::DEBUG),
        );
        array_walk($expected, array($this, '__callback_testFormatBatchNewlines'));
        $this->assertEquals(implode("\n", $expected), $formatter->formatBatch($records));
    }

    public function __callback_testFormatBatchNewlines(&$value, $key)
    {
        $value = json_encode($value);
    }
}

<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class FlowdockFormatterTest extends ehough_epilog_TestCase
{
    /**
     * @covers ehough_epilog_formatter_FlowdockFormatter::format
     */
    public function testFormat()
    {
        $formatter = new ehough_epilog_formatter_FlowdockFormatter('test_source', 'source@test.com');
        $record = $this->getRecord();

        $expected = array(
            'source' => 'test_source',
            'from_address' => 'source@test.com',
            'subject' => 'in test_source: WARNING - test',
            'content' => 'test',
            'tags' => array('#logs', '#warning', '#test'),
            'project' => 'test_source',
        );
        $formatted = $formatter->format($record);

        $this->assertEquals($expected, $formatted['flowdock']);
    }

    /**
     * @ covers ehough_epilog_formatter_FlowdockFormatter::formatBatch
     */
    public function testFormatBatch()
    {
        $formatter = new ehough_epilog_formatter_FlowdockFormatter('test_source', 'source@test.com');
        $records = array(
            $this->getRecord(ehough_epilog_Logger::WARNING),
            $this->getRecord(ehough_epilog_Logger::DEBUG),
        );
        $formatted = $formatter->formatBatch($records);

        $this->assertArrayHasKey('flowdock', $formatted[0]);
        $this->assertArrayHasKey('flowdock', $formatted[1]);
    }
}

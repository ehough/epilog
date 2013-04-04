<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class LogstashFormatterTest extends PHPUnit_Framework_TestCase
{

    /**
     * @covers ehough_epilog_formatter_LogstashFormatter::format
     */
    public function testDefaultFormatter()
    {
        $formatter = new ehough_epilog_formatter_LogstashFormatter('test', 'hostname');
        $record = array(
            'level' => ehough_epilog_Logger::ERROR,
            'level_name' => 'ERROR',
            'channel' => 'meh',
            'context' => array(),
            'datetime' => new DateTime("@0"),
            'extra' => array(),
            'message' => 'log',
        );

        $message = json_decode($formatter->format($record), true);

        $this->assertEquals("1970-01-01T00:00:00+00:00", $message['@timestamp']);
        $this->assertEquals('log', $message['@message']);
        $this->assertEquals('meh', $message['@fields']['channel']);
        $this->assertContains('meh', $message['@tags']);
        $this->assertEquals(ehough_epilog_Logger::ERROR, $message['@fields']['level']);
        $this->assertEquals('test', $message['@type']);
        $this->assertEquals('hostname', $message['@source']);

        $formatter = new ehough_epilog_formatter_LogstashFormatter('mysystem');

        $message = json_decode($formatter->format($record), true);

        $this->assertEquals('mysystem', $message['@type']);
    }

    /**
     * @covers ehough_epilog_formatter_LogstashFormatter::format
     */
    public function testFormatWithFileAndLine()
    {
        $formatter = new ehough_epilog_formatter_LogstashFormatter('test');
        $record = array(
            'level' => ehough_epilog_Logger::ERROR,
            'level_name' => 'ERROR',
            'channel' => 'meh',
            'context' => array('from' => 'logger'),
            'datetime' => new DateTime("@0"),
            'extra' => array('file' => 'test', 'line' => 14),
            'message' => 'log',
        );

        $message = json_decode($formatter->format($record), true);

        $this->assertEquals('test', $message['@fields']['file']);
        $this->assertEquals(14, $message['@fields']['line']);
    }

    /**
     * @covers ehough_epilog_formatter_LogstashFormatter::format
     */
    public function testFormatWithContext()
    {
        $formatter = new ehough_epilog_formatter_LogstashFormatter('test');
        $record = array(
            'level' => ehough_epilog_Logger::ERROR,
            'level_name' => 'ERROR',
            'channel' => 'meh',
            'context' => array('from' => 'logger'),
            'datetime' => new DateTime("@0"),
            'extra' => array('key' => 'pair'),
            'message' => 'log'
        );

        $message = json_decode($formatter->format($record), true);

        $message_array = $message['@fields'];

        $this->assertArrayHasKey('ctxt_from', $message_array);
        $this->assertEquals('logger', $message_array['ctxt_from']);

        // Test with extraPrefix
        $formatter = new ehough_epilog_formatter_LogstashFormatter('test', null, null, 'CTX');
        $message = json_decode($formatter->format($record), true);

        $message_array = $message['@fields'];

        $this->assertArrayHasKey('CTXfrom', $message_array);
        $this->assertEquals('logger', $message_array['CTXfrom']);

    }

    /**
     * @covers ehough_epilog_formatter_LogstashFormatter::format
     */
    public function testFormatWithExtra()
    {
        $formatter = new ehough_epilog_formatter_LogstashFormatter('test');
        $record = array(
            'level' => ehough_epilog_Logger::ERROR,
            'level_name' => 'ERROR',
            'channel' => 'meh',
            'context' => array('from' => 'logger'),
            'datetime' => new DateTime("@0"),
            'extra' => array('key' => 'pair'),
            'message' => 'log'
        );

        $message = json_decode($formatter->format($record), true);

        $message_array = $message['@fields'];

        $this->assertArrayHasKey('key', $message_array);
        $this->assertEquals('pair', $message_array['key']);

        // Test with extraPrefix
        $formatter = new ehough_epilog_formatter_LogstashFormatter('test', null, 'EXT');
        $message = json_decode($formatter->format($record), true);

        $message_array = $message['@fields'];

        $this->assertArrayHasKey('EXTkey', $message_array);
        $this->assertEquals('pair', $message_array['EXTkey']);
    }

    public function testFormatWithApplicationName()
    {
        $formatter = new ehough_epilog_formatter_LogstashFormatter('app', 'test');
        $record = array(
            'level' => ehough_epilog_Logger::ERROR,
            'level_name' => 'ERROR',
            'channel' => 'meh',
            'context' => array('from' => 'logger'),
            'datetime' => new DateTime("@0"),
            'extra' => array('key' => 'pair'),
            'message' => 'log'
        );

        $message = json_decode($formatter->format($record), true);

        $this->assertArrayHasKey('@type', $message);
        $this->assertEquals('app', $message['@type']);
    }
}

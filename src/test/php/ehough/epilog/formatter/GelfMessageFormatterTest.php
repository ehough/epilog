<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class GelfMessageFormatterTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (version_compare(PHP_VERSION, '5.3.0') < 0 || !class_exists("Gelf\Message")) {
            $this->markTestSkipped("mlehner/gelf-php not installed");
        }
    }

    /**
     * @covers Monolog\Formatter\GelfMessageFormatter::format
     */
    public function testDefaultFormatter()
    {
        $formatter = new ehough_epilog_formatter_GelfMessageFormatter();
        $record = array(
            'level' => ehough_epilog_Logger::ERROR,
            'level_name' => 'ERROR',
            'channel' => 'meh',
            'context' => array(),
            'datetime' => new DateTime("@0"),
            'extra' => array(),
            'message' => 'log',
        );

        $message = $formatter->format($record);

        $this->assertInstanceOf('Gelf\Message', $message);
        $this->assertEquals(0, $message->getTimestamp());
        $this->assertEquals('log', $message->getShortMessage());
        $this->assertEquals('meh', $message->getFacility());
        $this->assertEquals(null, $message->getLine());
        $this->assertEquals(null, $message->getFile());
        $this->assertEquals(3, $message->getLevel());
        $this->assertNotEmpty($message->getHost());

        $formatter = new ehough_epilog_formatter_GelfMessageFormatter('mysystem');

        $message = $formatter->format($record);

        $this->assertInstanceOf('Gelf\Message', $message);
        $this->assertEquals('mysystem', $message->getHost());
    }

    /**
     * @covers Monolog\Formatter\GelfMessageFormatter::format
     */
    public function testFormatWithFileAndLine()
    {
        $formatter = new ehough_epilog_formatter_GelfMessageFormatter();
        $record = array(
            'level' => ehough_epilog_Logger::ERROR,
            'level_name' => 'ERROR',
            'channel' => 'meh',
            'context' => array('from' => 'logger'),
            'datetime' => new DateTime("@0"),
            'extra' => array('file' => 'test', 'line' => 14),
            'message' => 'log',
        );

        $message = $formatter->format($record);

        $this->assertInstanceOf('Gelf\Message', $message);
        $this->assertEquals('test', $message->getFile());
        $this->assertEquals(14, $message->getLine());
    }

    /**
     * @covers Monolog\Formatter\GelfMessageFormatter::format
     */
    public function testFormatWithContext()
    {
        $formatter = new ehough_epilog_formatter_GelfMessageFormatter();
        $record = array(
            'level' => ehough_epilog_Logger::ERROR,
            'level_name' => 'ERROR',
            'channel' => 'meh',
            'context' => array('from' => 'logger'),
            'datetime' => new DateTime("@0"),
            'extra' => array('key' => 'pair'),
            'message' => 'log'
        );

        $message = $formatter->format($record);

        $this->assertInstanceOf('Gelf\Message', $message);

        $message_array = $message->toArray();

        $this->assertArrayHasKey('_ctxt_from', $message_array);
        $this->assertEquals('logger', $message_array['_ctxt_from']);

        // Test with extraPrefix
        $formatter = new ehough_epilog_formatter_GelfMessageFormatter(null, null, 'CTX');
        $message = $formatter->format($record);

        $this->assertInstanceOf('Gelf\Message', $message);

        $message_array = $message->toArray();

        $this->assertArrayHasKey('_CTXfrom', $message_array);
        $this->assertEquals('logger', $message_array['_CTXfrom']);

    }

    /**
     * @covers Monolog\Formatter\GelfMessageFormatter::format
     */
    public function testFormatWithExtra()
    {
        $formatter = new ehough_epilog_formatter_GelfMessageFormatter();
        $record = array(
            'level' => ehough_epilog_Logger::ERROR,
            'level_name' => 'ERROR',
            'channel' => 'meh',
            'context' => array('from' => 'logger'),
            'datetime' => new DateTime("@0"),
            'extra' => array('key' => 'pair'),
            'message' => 'log'
        );

        $message = $formatter->format($record);

        $this->assertInstanceOf('Gelf\Message', $message);

        $message_array = $message->toArray();

        $this->assertArrayHasKey('_key', $message_array);
        $this->assertEquals('pair', $message_array['_key']);

        // Test with extraPrefix
        $formatter = new ehough_epilog_formatter_GelfMessageFormatter(null, 'EXT');
        $message = $formatter->format($record);

        $this->assertInstanceOf('Gelf\Message', $message);

        $message_array = $message->toArray();

        $this->assertArrayHasKey('_EXTkey', $message_array);
        $this->assertEquals('pair', $message_array['_EXTkey']);
    }
}

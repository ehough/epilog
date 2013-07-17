<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * @covers ehough_epilog_formatter_NormalizerFormatter
 */
class NormalizerFormatterTest extends PHPUnit_Framework_TestCase
{
    public function testFormat()
    {
        $formatter = new ehough_epilog_formatter_NormalizerFormatter('Y-m-d');
        $formatted = $formatter->format(array(
            'level_name' => 'ERROR',
            'channel' => 'meh',
            'message' => 'foo',
            'datetime' => new DateTime,
            'extra' => array('foo' => new TestFooNorm, 'bar' => new TestBarNorm, 'baz' => array(), 'res' => fopen('php://memory', 'rb')),
            'context' => array(
                'foo' => 'bar',
                'baz' => 'qux',
            ),
        ));

        $this->assertEquals(array(
            'level_name' => 'ERROR',
            'channel' => 'meh',
            'message' => 'foo',
            'datetime' => date('Y-m-d'),
            'extra' => array(
                'foo' => '[object] (TestFooNorm: {"foo":"foo"})',
                'bar' => '[object] (TestBarNorm: {})',
                'baz' => array(),
                'res' => '[resource]',
            ),
            'context' => array(
                'foo' => 'bar',
                'baz' => 'qux',
            )
        ), $formatted);
    }

    public function testFormatExceptions()
    {
        $formatter = new ehough_epilog_formatter_NormalizerFormatter('Y-m-d');
        $e = new LogicException('bar');
        $e2 = new RuntimeException('foo', 0, $e);
        $formatted = $formatter->format(array(
            'exception' => $e2,
        ));

        $this->assertGreaterThan(5, count($formatted['exception']['trace']));
        $this->assertTrue(isset($formatted['exception']['previous']));
        unset($formatted['exception']['trace'], $formatted['exception']['previous']);

        $this->assertEquals(array(
            'exception' => array(
                'class'   => get_class($e2),
                'message' => $e2->getMessage(),
                'file'   => $e2->getFile().':'.$e2->getLine(),
            )
        ), $formatted);
    }

    public function testBatchFormat()
    {
        $formatter = new ehough_epilog_formatter_NormalizerFormatter('Y-m-d');
        $formatted = $formatter->formatBatch(array(
            array(
                'level_name' => 'CRITICAL',
                'channel' => 'test',
                'message' => 'bar',
                'context' => array(),
                'datetime' => new DateTime,
                'extra' => array(),
            ),
            array(
                'level_name' => 'WARNING',
                'channel' => 'log',
                'message' => 'foo',
                'context' => array(),
                'datetime' => new DateTime,
                'extra' => array(),
            ),
        ));
        $this->assertEquals(array(
            array(
                'level_name' => 'CRITICAL',
                'channel' => 'test',
                'message' => 'bar',
                'context' => array(),
                'datetime' => date('Y-m-d'),
                'extra' => array(),
            ),
            array(
                'level_name' => 'WARNING',
                'channel' => 'log',
                'message' => 'foo',
                'context' => array(),
                'datetime' => date('Y-m-d'),
                'extra' => array(),
            ),
        ), $formatted);
    }

    /**
     * Test issue #137
     */
    public function testIgnoresRecursiveObjectReferences()
    {
        if (version_compare(PHP_VERSION, '5.3') < 0) {

            $this->markTestSkipped('PHP < 5.3');
        }

        // set up the recursion
        $foo = new stdClass();
        $bar = new stdClass();

        $foo->bar = $bar;
        $bar->foo = $foo;

        // set an error handler to assert that the error is not raised anymore
        $that = $this;
        set_error_handler(array($this, '_callbackTestIgnoresRecursiveObjectReferences'));

        $formatter = new ehough_epilog_formatter_NormalizerFormatter();
        $reflMethod = new ReflectionMethod($formatter, 'toJson');
        $reflMethod->setAccessible(true);
        $res = $reflMethod->invoke($formatter, array($foo, $bar), true);

        restore_error_handler();

        $this->assertEquals(@json_encode(array($foo, $bar)), $res);
    }

    public function _callbackTestIgnoresRecursiveObjectReferences($level, $message, $file, $line, $context)
    {
        if (error_reporting() & $level) {
            restore_error_handler();
            $this->fail("$message should not be raised");
        }
    }

    public function testIgnoresInvalidTypes()
    {
        if (version_compare(PHP_VERSION, '5.3') < 0) {

            $this->markTestSkipped('PHP < 5.3');
        }

        // set up the recursion
        $resource = fopen(__FILE__, 'r');

        // set an error handler to assert that the error is not raised anymore
        $that = $this;
        set_error_handler(array($this, '_callbackTestIgnoresInvalidTypes'));

        $formatter = new ehough_epilog_formatter_NormalizerFormatter();
        $reflMethod = new ReflectionMethod($formatter, 'toJson');
        $reflMethod->setAccessible(true);
        $res = $reflMethod->invoke($formatter, array($resource), true);

        restore_error_handler();

        $this->assertEquals(@json_encode(array($resource)), $res);
    }

    public function _callbackTestIgnoresInvalidTypes($level, $message, $file, $line, $context)
    {
        if (error_reporting() & $level) {
            restore_error_handler();
            $this->fail("$message should not be raised");
        }
    }
}

class TestFooNorm
{
    public $foo = 'foo';
}

class TestBarNorm
{
    public function __toString()
    {
        return 'bar';
    }
}

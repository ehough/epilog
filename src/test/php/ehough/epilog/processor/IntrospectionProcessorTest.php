<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Tester
{
    public function test($handler, $record)
    {
        $handler->handle($record);
    }
}

function tester($handler, $record)
{
    $handler->handle($record);
}

class ehough_epilog_processor_IntrospectionProcessorTest extends ehough_epilog_TestCase
{
    public function getHandler()
    {
        $processor = new ehough_epilog_processor_IntrospectionProcessor();
        $handler = new ehough_epilog_handler_TestHandler();
        $handler->pushProcessor($processor);

        return $handler;
    }

    public function testProcessorFromClass()
    {
        $handler = $this->getHandler();
        $tester = new Tester;
        $tester->test($handler, $this->getRecord());
        list($record) = $handler->getRecords();
        $this->assertEquals(__FILE__, $record['extra']['file']);
        $this->assertEquals(16, $record['extra']['line']);
        $this->assertEquals('Tester', $record['extra']['class']);
        $this->assertEquals('test', $record['extra']['function']);
    }

    public function testProcessorFromFunc()
    {
        $handler = $this->getHandler();
        tester($handler, $this->getRecord());
        list($record) = $handler->getRecords();
        $this->assertEquals(__FILE__, $record['extra']['file']);
        $this->assertEquals(22, $record['extra']['line']);
        $this->assertEquals(null, $record['extra']['class']);
        $this->assertEquals('tester', $record['extra']['function']);
    }

    public function testLevelTooLow()
    {
        $input = array(
            'level' => ehough_epilog_Logger::DEBUG,
            'extra' => array(),
        );

        $expected = $input;

        $processor = new ehough_epilog_processor_IntrospectionProcessor(ehough_epilog_Logger::CRITICAL);
        $actual = $processor($input);

        $this->assertEquals($expected, $actual);
    }

    public function testLevelEqual()
    {
        $input = array(
            'level' => ehough_epilog_Logger::CRITICAL,
            'extra' => array(),
        );

        $expected = $input;
        $expected['extra'] = array(
            'file' => null,
            'line' => null,
            'class' => 'ReflectionMethod',
            'function' => 'invokeArgs',
        );

        $processor = new ehough_epilog_processor_IntrospectionProcessor(ehough_epilog_Logger::CRITICAL);
        $actual = $processor($input);

        $this->assertEquals($expected, $actual);
    }

    public function testLevelHigher()
    {
        $input = array(
            'level' => ehough_epilog_Logger::EMERGENCY,
            'extra' => array(),
        );

        $expected = $input;
        $expected['extra'] = array(
            'file' => null,
            'line' => null,
            'class' => 'ReflectionMethod',
            'function' => 'invokeArgs',
        );

        $processor = new ehough_epilog_processor_IntrospectionProcessor(ehough_epilog_Logger::CRITICAL);
        $actual = $processor($input);

        $this->assertEquals($expected, $actual);
    }
}

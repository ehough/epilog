<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//namespace Monolog\Processor;

//use Monolog\TestCase;
//use ehough_epilog_handler_TestHandler;

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
        $this->assertEquals(58, $record['extra']['line']);
        $this->assertEquals('Tester', $record['extra']['class']);
        $this->assertEquals('test', $record['extra']['function']);
    }

    public function testProcessorFromFunc()
    {
        $handler = $this->getHandler();
        tester($handler, $this->getRecord());
        list($record) = $handler->getRecords();
        $this->assertEquals(__FILE__, $record['extra']['file']);
        $this->assertEquals(64, $record['extra']['line']);
        $this->assertEquals(null, $record['extra']['class']);
        $this->assertEquals('tester', $record['extra']['function']);
    }
}

//namespace Acme;

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

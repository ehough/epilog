<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//namespace Monolog\Handler;

//use Monolog\TestCase;
//use Monolog\Logger;

class ehough_epilog_handler_GroupHandlerTest extends ehough_epilog_TestCase
{
    /**
     * @covers ehough_epilog_handler_GroupHandler::__construct
     * @expectedException InvalidArgumentException
     */
    public function testConstructorOnlyTakesHandler()
    {
        new ehough_epilog_handler_GroupHandler(array(new ehough_epilog_handler_TestHandler(), "foo"));
    }

    /**
     * @covers ehough_epilog_handler_GroupHandler::__construct
     * @covers ehough_epilog_handler_GroupHandler::handle
     */
    public function testHandle()
    {
        $testHandlers = array(new ehough_epilog_handler_TestHandler(), new ehough_epilog_handler_TestHandler());
        $handler = new ehough_epilog_handler_GroupHandler($testHandlers);
        $handler->handle($this->getRecord(ehough_epilog_Logger::DEBUG));
        $handler->handle($this->getRecord(ehough_epilog_Logger::INFO));
        foreach ($testHandlers as $test) {
            $this->assertTrue($test->hasDebugRecords());
            $this->assertTrue($test->hasInfoRecords());
            $this->assertTrue(count($test->getRecords()) === 2);
        }
    }

    /**
     * @covers ehough_epilog_handler_GroupHandler::handleBatch
     */
    public function testHandleBatch()
    {
        $testHandlers = array(new ehough_epilog_handler_TestHandler(), new ehough_epilog_handler_TestHandler());
        $handler = new ehough_epilog_handler_GroupHandler($testHandlers);
        $handler->handleBatch(array($this->getRecord(ehough_epilog_Logger::DEBUG), $this->getRecord(ehough_epilog_Logger::INFO)));
        foreach ($testHandlers as $test) {
            $this->assertTrue($test->hasDebugRecords());
            $this->assertTrue($test->hasInfoRecords());
            $this->assertTrue(count($test->getRecords()) === 2);
        }
    }

    /**
     * @covers ehough_epilog_handler_GroupHandler::isHandling
     */
    public function testIsHandling()
    {
        $testHandlers = array(new ehough_epilog_handler_TestHandler(ehough_epilog_Logger::ERROR), new ehough_epilog_handler_TestHandler(ehough_epilog_Logger::WARNING));
        $handler = new ehough_epilog_handler_GroupHandler($testHandlers);
        $this->assertTrue($handler->isHandling($this->getRecord(ehough_epilog_Logger::ERROR)));
        $this->assertTrue($handler->isHandling($this->getRecord(ehough_epilog_Logger::WARNING)));
        $this->assertFalse($handler->isHandling($this->getRecord(ehough_epilog_Logger::DEBUG)));
    }

    /**
     * @covers ehough_epilog_handler_GroupHandler::handle
     */
    public function testHandleUsesProcessors()
    {
        $test = new ehough_epilog_handler_TestHandler();
        $handler = new ehough_epilog_handler_GroupHandler(array($test));
        $handler->pushProcessor(function ($record) {
            $record['extra']['foo'] = true;

            return $record;
        });
        $handler->handle($this->getRecord(ehough_epilog_Logger::WARNING));
        $this->assertTrue($test->hasWarningRecords());
        $records = $test->getRecords();
        $this->assertTrue($records[0]['extra']['foo']);
    }
}

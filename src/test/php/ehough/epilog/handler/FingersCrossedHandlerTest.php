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
//use Monolog\Handler\FingersCrossed\ErrorLevelActivationStrategy;

class ehough_epilog_handler_FingersCrossedHandlerTest extends ehough_epilog_TestCase
{
    /**
     * @covers ehough_epilog_handler_FingersCrossedHandler::__construct
     * @covers ehough_epilog_handler_FingersCrossedHandler::handle
     */
    public function testHandleBuffers()
    {
        $test = new ehough_epilog_handler_TestHandler();
        $handler = new ehough_epilog_handler_FingersCrossedHandler($test);
        $handler->handle($this->getRecord(ehough_epilog_Logger::DEBUG));
        $handler->handle($this->getRecord(ehough_epilog_Logger::INFO));
        $this->assertFalse($test->hasDebugRecords());
        $this->assertFalse($test->hasInfoRecords());
        $handler->handle($this->getRecord(ehough_epilog_Logger::WARNING));
        $this->assertTrue($test->hasInfoRecords());
        $this->assertTrue(count($test->getRecords()) === 3);
    }

    /**
     * @covers ehough_epilog_handler_FingersCrossedHandler::handle
     */
    public function testHandleStopsBufferingAfterTrigger()
    {
        $test = new ehough_epilog_handler_TestHandler();
        $handler = new ehough_epilog_handler_FingersCrossedHandler($test);
        $handler->handle($this->getRecord(ehough_epilog_Logger::WARNING));
        $handler->handle($this->getRecord(ehough_epilog_Logger::DEBUG));
        $this->assertTrue($test->hasWarningRecords());
        $this->assertTrue($test->hasDebugRecords());
    }

    /**
     * @covers ehough_epilog_handler_FingersCrossedHandler::handle
     * @covers ehough_epilog_handler_FingersCrossedHandler::reset
     */
    public function testHandleRestartBufferingAfterReset()
    {
        $test = new ehough_epilog_handler_TestHandler();
        $handler = new ehough_epilog_handler_FingersCrossedHandler($test);
        $handler->handle($this->getRecord(ehough_epilog_Logger::WARNING));
        $handler->handle($this->getRecord(ehough_epilog_Logger::DEBUG));
        $handler->reset();
        $handler->handle($this->getRecord(ehough_epilog_Logger::INFO));
        $this->assertTrue($test->hasWarningRecords());
        $this->assertTrue($test->hasDebugRecords());
        $this->assertFalse($test->hasInfoRecords());
    }

    /**
     * @covers ehough_epilog_handler_FingersCrossedHandler::handle
     */
    public function testHandleRestartBufferingAfterBeingTriggeredWhenStopBufferingIsDisabled()
    {
        $test = new ehough_epilog_handler_TestHandler();
        $handler = new ehough_epilog_handler_FingersCrossedHandler($test, ehough_epilog_Logger::WARNING, 0, false, false);
        $handler->handle($this->getRecord(ehough_epilog_Logger::DEBUG));
        $handler->handle($this->getRecord(ehough_epilog_Logger::WARNING));
        $handler->handle($this->getRecord(ehough_epilog_Logger::INFO));
        $this->assertTrue($test->hasWarningRecords());
        $this->assertTrue($test->hasDebugRecords());
        $this->assertFalse($test->hasInfoRecords());
    }

    /**
     * @covers ehough_epilog_handler_FingersCrossedHandler::handle
     */
    public function testHandleBufferLimit()
    {
        $test = new ehough_epilog_handler_TestHandler();
        $handler = new ehough_epilog_handler_FingersCrossedHandler($test, ehough_epilog_Logger::WARNING, 2);
        $handler->handle($this->getRecord(ehough_epilog_Logger::DEBUG));
        $handler->handle($this->getRecord(ehough_epilog_Logger::DEBUG));
        $handler->handle($this->getRecord(ehough_epilog_Logger::INFO));
        $handler->handle($this->getRecord(ehough_epilog_Logger::WARNING));
        $this->assertTrue($test->hasWarningRecords());
        $this->assertTrue($test->hasInfoRecords());
        $this->assertFalse($test->hasDebugRecords());
    }

    /**
     * @covers ehough_epilog_handler_FingersCrossedHandler::handle
     */
    public function testHandleWithCallback()
    {
        $test = new ehough_epilog_handler_TestHandler();
        $handler = new ehough_epilog_handler_FingersCrossedHandler(function($record, $handler) use ($test) {
                    return $test;
                });
        $handler->handle($this->getRecord(ehough_epilog_Logger::DEBUG));
        $handler->handle($this->getRecord(ehough_epilog_Logger::INFO));
        $this->assertFalse($test->hasDebugRecords());
        $this->assertFalse($test->hasInfoRecords());
        $handler->handle($this->getRecord(ehough_epilog_Logger::WARNING));
        $this->assertTrue($test->hasInfoRecords());
        $this->assertTrue(count($test->getRecords()) === 3);
    }

    /**
     * @covers ehough_epilog_handler_FingersCrossedHandler::handle
     * @expectedException RuntimeException
     */
    public function testHandleWithBadCallbackThrowsException()
    {
        $handler = new ehough_epilog_handler_FingersCrossedHandler(function($record, $handler) {
                    return 'foo';
                });
        $handler->handle($this->getRecord(ehough_epilog_Logger::WARNING));
    }

    /**
     * @covers ehough_epilog_handler_FingersCrossedHandler::isHandling
     */
    public function testIsHandlingAlways()
    {
        $test = new ehough_epilog_handler_TestHandler();
        $handler = new ehough_epilog_handler_FingersCrossedHandler($test, ehough_epilog_Logger::ERROR);
        $this->assertTrue($handler->isHandling($this->getRecord(ehough_epilog_Logger::DEBUG)));
    }

    /**
     * @covers ehough_epilog_handler_FingersCrossedHandler::__construct
     */
    public function testActivationStrategy()
    {
        $test = new ehough_epilog_handler_TestHandler();
        $handler = new ehough_epilog_handler_FingersCrossedHandler($test, new ehough_epilog_handler_fingerscrossed_ErrorLevelActivationStrategy(ehough_epilog_Logger::WARNING));
        $handler->handle($this->getRecord(ehough_epilog_Logger::DEBUG));
        $this->assertFalse($test->hasDebugRecords());
        $handler->handle($this->getRecord(ehough_epilog_Logger::WARNING));
        $this->assertTrue($test->hasDebugRecords());
        $this->assertTrue($test->hasWarningRecords());
    }

    /**
     * @covers ehough_epilog_handler_FingersCrossedHandler::handle
     */
    public function testHandleUsesProcessors()
    {
        $test = new ehough_epilog_handler_TestHandler();
        $handler = new ehough_epilog_handler_FingersCrossedHandler($test, ehough_epilog_Logger::INFO);
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

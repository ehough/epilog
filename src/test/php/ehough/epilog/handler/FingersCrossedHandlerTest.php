<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
        $handler->close();
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
        $handler->close();
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
        $handler->close();
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
        $handler->close();
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
        $this->_test = $test;
        $handler = new ehough_epilog_handler_FingersCrossedHandler(array($this, '_callbackTestHandleWithCallback'));
        $handler->handle($this->getRecord(ehough_epilog_Logger::DEBUG));
        $handler->handle($this->getRecord(ehough_epilog_Logger::INFO));
        $this->assertFalse($test->hasDebugRecords());
        $this->assertFalse($test->hasInfoRecords());
        $handler->handle($this->getRecord(ehough_epilog_Logger::WARNING));
        $this->assertTrue($test->hasInfoRecords());
        $this->assertTrue(count($test->getRecords()) === 3);
    }

    public function _callbackTestHandleWithCallback($record, $handler)
    {
        return $this->_test;
    }

    /**
     * @covers ehough_epilog_handler_FingersCrossedHandler::handle
     * @expectedException RuntimeException
     */
    public function testHandleWithBadCallbackThrowsException()
    {
        $handler = new ehough_epilog_handler_FingersCrossedHandler(array($this, '_callbackTestHandleWithBadCallbackThrowsException'));
        $handler->handle($this->getRecord(ehough_epilog_Logger::WARNING));
    }

    public function _callbackTestHandleWithBadCallbackThrowsException($record, $handler)
    {
        return 'foo';
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
     * @covers ehough_epilog_handler_fingerscrossed_ErrorLevelActivationStrategy::__construct
     * @covers ehough_epilog_handler_fingerscrossed_ErrorLevelActivationStrategy::isHandlerActivated
     */
    public function testErrorLevelActivationStrategy()
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
     * @covers ehough_epilog_handler_fingerscrossed_ChannelLevelActivationStrategy::__construct
     * @covers ehough_epilog_handler_fingerscrossed_ChannelLevelActivationStrategy::isHandlerActivated
     */
    public function testChannelLevelActivationStrategy()
    {
        $test = new ehough_epilog_handler_TestHandler();
        $handler = new ehough_epilog_handler_FingersCrossedHandler($test, new ehough_epilog_handler_fingerscrossed_ChannelLevelActivationStrategy(ehough_epilog_Logger::ERROR, array('othertest' => ehough_epilog_Logger::DEBUG)));
        $handler->handle($this->getRecord(ehough_epilog_Logger::WARNING));
        $this->assertFalse($test->hasWarningRecords());
        $record = $this->getRecord(ehough_epilog_Logger::DEBUG);
        $record['channel'] = 'othertest';
        $handler->handle($record);
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
        $handler->pushProcessor(array($this, '_callbackTestHandleUsesProcessors'));
        $handler->handle($this->getRecord(ehough_epilog_Logger::WARNING));
        $this->assertTrue($test->hasWarningRecords());
        $records = $test->getRecords();
        $this->assertTrue($records[0]['extra']['foo']);
    }

    /**
     * @covers ehough_epilog_handler_FingersCrossedHandler::close
     */
    public function testPassthruOnClose()
    {
        $test = new ehough_epilog_handler_TestHandler();
        $handler = new ehough_epilog_handler_FingersCrossedHandler($test, new ehough_epilog_handler_fingerscrossed_ErrorLevelActivationStrategy(ehough_epilog_Logger::WARNING), 0, true, true, ehough_epilog_Logger::INFO);
        $handler->handle($this->getRecord(ehough_epilog_Logger::DEBUG));
        $handler->handle($this->getRecord(ehough_epilog_Logger::INFO));
        $handler->close();
        $this->assertFalse($test->hasDebugRecords());
        $this->assertTrue($test->hasInfoRecords());
    }

    public function _callbackTestHandleUsesProcessors($record)
    {
        $record['extra']['foo'] = true;

        return $record;
    }
}

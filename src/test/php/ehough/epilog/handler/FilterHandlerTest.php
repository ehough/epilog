<?php

class FilterHandlerTest extends ehough_epilog_TestCase
{
    /**
     * @covers ehough_epilog_handler_FilterHandler::isHandling
     */
    public function testIsHandling()
    {
        $test    = new ehough_epilog_handler_TestHandler();
        $handler = new ehough_epilog_handler_FilterHandler($test, ehough_epilog_Logger::INFO, ehough_epilog_Logger::NOTICE);
        $this->assertFalse($handler->isHandling($this->getRecord(ehough_epilog_Logger::DEBUG)));
        $this->assertTrue($handler->isHandling($this->getRecord(ehough_epilog_Logger::INFO)));
        $this->assertTrue($handler->isHandling($this->getRecord(ehough_epilog_Logger::NOTICE)));
        $this->assertFalse($handler->isHandling($this->getRecord(ehough_epilog_Logger::WARNING)));
        $this->assertFalse($handler->isHandling($this->getRecord(ehough_epilog_Logger::ERROR)));
        $this->assertFalse($handler->isHandling($this->getRecord(ehough_epilog_Logger::CRITICAL)));
        $this->assertFalse($handler->isHandling($this->getRecord(ehough_epilog_Logger::ALERT)));
        $this->assertFalse($handler->isHandling($this->getRecord(ehough_epilog_Logger::EMERGENCY)));
    }

    /**
     * @covers ehough_epilog_handler_FilterHandler::handle
     * @covers ehough_epilog_handler_FilterHandler::setAcceptedLevels
     * @covers ehough_epilog_handler_FilterHandler::isHandling
     */
    public function testHandleProcessOnlyNeededLevels()
    {
        $test    = new ehough_epilog_handler_TestHandler();
        $handler = new ehough_epilog_handler_FilterHandler($test, ehough_epilog_Logger::INFO, ehough_epilog_Logger::NOTICE);

        $handler->handle($this->getRecord(ehough_epilog_Logger::DEBUG));
        $this->assertFalse($test->hasDebugRecords());

        $handler->handle($this->getRecord(ehough_epilog_Logger::INFO));
        $this->assertTrue($test->hasInfoRecords());
        $handler->handle($this->getRecord(ehough_epilog_Logger::NOTICE));
        $this->assertTrue($test->hasNoticeRecords());

        $handler->handle($this->getRecord(ehough_epilog_Logger::WARNING));
        $this->assertFalse($test->hasWarningRecords());
        $handler->handle($this->getRecord(ehough_epilog_Logger::ERROR));
        $this->assertFalse($test->hasErrorRecords());
        $handler->handle($this->getRecord(ehough_epilog_Logger::CRITICAL));
        $this->assertFalse($test->hasCriticalRecords());
        $handler->handle($this->getRecord(ehough_epilog_Logger::ALERT));
        $this->assertFalse($test->hasAlertRecords());
        $handler->handle($this->getRecord(ehough_epilog_Logger::EMERGENCY));
        $this->assertFalse($test->hasEmergencyRecords());

        $test    = new ehough_epilog_handler_TestHandler();
        $handler = new ehough_epilog_handler_FilterHandler($test, array(ehough_epilog_Logger::INFO, ehough_epilog_Logger::ERROR));

        $handler->handle($this->getRecord(ehough_epilog_Logger::DEBUG));
        $this->assertFalse($test->hasDebugRecords());
        $handler->handle($this->getRecord(ehough_epilog_Logger::INFO));
        $this->assertTrue($test->hasInfoRecords());
        $handler->handle($this->getRecord(ehough_epilog_Logger::NOTICE));
        $this->assertFalse($test->hasNoticeRecords());
        $handler->handle($this->getRecord(ehough_epilog_Logger::ERROR));
        $this->assertTrue($test->hasErrorRecords());
        $handler->handle($this->getRecord(ehough_epilog_Logger::CRITICAL));
        $this->assertFalse($test->hasCriticalRecords());
    }

    /**
     * @covers ehough_epilog_handler_FilterHandler::setAcceptedLevels
     * @covers ehough_epilog_handler_FilterHandler::getAcceptedLevels
     */
    public function testAcceptedLevelApi()
    {
        $test    = new ehough_epilog_handler_TestHandler();
        $handler = new ehough_epilog_handler_FilterHandler($test);

        $levels = array(ehough_epilog_Logger::INFO, ehough_epilog_Logger::ERROR);
        $handler->setAcceptedLevels($levels);
        $this->assertSame($levels, $handler->getAcceptedLevels());
    }

    /**
     * @covers ehough_epilog_handler_FilterHandler::handle
     */
    public function testHandleUsesProcessors()
    {
        $test    = new ehough_epilog_handler_TestHandler();
        $handler = new ehough_epilog_handler_FilterHandler($test, ehough_epilog_Logger::DEBUG, ehough_epilog_Logger::EMERGENCY);
        $handler->pushProcessor(
            function ($record) {
                $record['extra']['foo'] = true;

                return $record;
            }
        );
        $handler->handle($this->getRecord(ehough_epilog_Logger::WARNING));
        $this->assertTrue($test->hasWarningRecords());
        $records = $test->getRecords();
        $this->assertTrue($records[0]['extra']['foo']);
    }

    /**
     * @covers ehough_epilog_handler_FilterHandler::handle
     */
    public function testHandlerespectsBubble()
    {
        $test = new ehough_epilog_handler_TestHandler();

        $handler = new ehough_epilog_handler_FilterHandler($test, ehough_epilog_Logger::INFO, ehough_epilog_Logger::NOTICE, false);
        $this->assertTrue($handler->handle($this->getRecord(ehough_epilog_Logger::INFO)));
        $this->assertFalse($handler->handle($this->getRecord(ehough_epilog_Logger::WARNING)));

        $handler = new ehough_epilog_handler_FilterHandler($test, ehough_epilog_Logger::INFO, ehough_epilog_Logger::NOTICE, true);
        $this->assertFalse($handler->handle($this->getRecord(ehough_epilog_Logger::INFO)));
        $this->assertFalse($handler->handle($this->getRecord(ehough_epilog_Logger::WARNING)));
    }

    /**
     * @covers ehough_epilog_handler_FilterHandler::handle
     */
    public function testHandleWithCallback()
    {
        $test    = new ehough_epilog_handler_TestHandler();
        $handler = new ehough_epilog_handler_FilterHandler(
            function ($record, $handler) use ($test) {
                return $test;
            }, ehough_epilog_Logger::INFO, ehough_epilog_Logger::NOTICE, false
        );
        $handler->handle($this->getRecord(ehough_epilog_Logger::DEBUG));
        $handler->handle($this->getRecord(ehough_epilog_Logger::INFO));
        $this->assertFalse($test->hasDebugRecords());
        $this->assertTrue($test->hasInfoRecords());
    }

    /**
     * @covers ehough_epilog_handler_FilterHandler::handle
     * @expectedException \RuntimeException
     */
    public function testHandleWithBadCallbackThrowsException()
    {
        $handler = new ehough_epilog_handler_FilterHandler(
            function ($record, $handler) {
                return 'foo';
            }
        );
        $handler->handle($this->getRecord(ehough_epilog_Logger::WARNING));
    }
}

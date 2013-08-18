<?php

class ehough_epilog_handler_ErrorLogHandlerTest extends ehough_epilog_TestCase
{
    private $_writtenRecord;

    protected function setUp()
    {
        $this->_writtenRecord = array();
    }

    public function __write()
    {
        $this->_writtenRecord = func_get_args();
    }

    /**
     * @covers ehough_epilog_handler_ErrorLogHandler::__construct
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage The given message type "42" is not supported
     */
    public function testShouldNotAcceptAnInvalidTypeOnContructor()
    {
        new ehough_epilog_handler_ErrorLogHandler(42);
    }

    /**
     * @covers ehough_epilog_handler_ErrorLogHandler::write
     */
    public function testShouldLogMessagesUsingErrorLogFuncion()
    {
        $type = ehough_epilog_handler_ErrorLogHandler::OPERATING_SYSTEM;
        $handler = new ehough_epilog_handler_ErrorLogHandler($type);

        $handler->__setWriter(array($this, '__write'));

        $handler->handle($this->getRecord(ehough_epilog_Logger::ERROR));

        $this->assertStringMatchesFormat('[%s] test.ERROR: test [] []', $this->_writtenRecord[0]);
        $this->assertSame($this->_writtenRecord[1], $type);
    }
}

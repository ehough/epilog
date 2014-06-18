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
        $this->_writtenRecord[] = func_get_args();
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
        $handler->setFormatter(new ehough_epilog_formatter_LineFormatter('%channel%.%level_name%: %message% %context% %extra%', null, true));
        $handler->__setWriter(array($this, '__write'));
        $handler->handle($this->getRecord(ehough_epilog_Logger::ERROR, "Foo\nBar\r\n\r\nBaz"));

        $this->assertSame("test.ERROR: Foo\nBar\r\n\r\nBaz [] []", $this->_writtenRecord[0][0]);
        $this->assertSame($this->_writtenRecord[0][1], $type);

        $handler = new ehough_epilog_handler_ErrorLogHandler($type, ehough_epilog_Logger::DEBUG, true, true);
        $handler->setFormatter(new ehough_epilog_formatter_LineFormatter(null, null, true));
        $handler->__setWriter(array($this, '__write'));
        $handler->handle($this->getRecord(ehough_epilog_Logger::ERROR, "Foo\nBar\r\n\r\nBaz"));

        $this->assertStringMatchesFormat('[%s] test.ERROR: Foo', $this->_writtenRecord[1][0]);
        $this->assertSame($this->_writtenRecord[1][1], $type);

        $this->assertStringMatchesFormat('Bar', $this->_writtenRecord[2][0]);
        $this->assertSame($this->_writtenRecord[2][1], $type);

        $this->assertStringMatchesFormat('Baz [] []', $this->_writtenRecord[3][0]);
        $this->assertSame($this->_writtenRecord[3][1], $type);
    }
}

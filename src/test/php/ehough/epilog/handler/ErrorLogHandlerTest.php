<?php

function error_log()
{
    $GLOBALS['error_log'] = func_get_args();
}

class ehough_epilog_handler_ErrorLogHandlerTest extends ehough_epilog_TestCase
{

    protected function setUp()
    {
        $GLOBALS['error_log'] = array();
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
        $handler->handle($this->getRecord(ehough_epilog_Logger::ERROR));

        $this->assertStringMatchesFormat('[%s] test.ERROR: test [] []', $GLOBALS['error_log'][0]);
        $this->assertSame($GLOBALS['error_log'][1], $type);
    }
}

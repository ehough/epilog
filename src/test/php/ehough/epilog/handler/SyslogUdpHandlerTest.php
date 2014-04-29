<?php

class ehough_epilog_handler_SyslogUdpHandlerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException UnexpectedValueException
     */
    public function testWeValidateFacilities()
    {
        $handler = new ehough_epilog_handler_SyslogUdpHandler("ip", null, "invalidFacility");
    }

    public function testWeSplitIntoLines()
    {
        if (!function_exists('socket_create')) {

            $this->markTestSkipped('socket_create() not available');
            return;
        }

        $handler = new ehough_epilog_handler_SyslogUdpHandler("127.0.0.1", 514, "authpriv");
        $handler->setFormatter(new ehough_epilog_formatter_ChromePHPFormatter());

        $socket = $this->getMock('ehough_epilog_handler_syslogudp_UdpSocket', array('write'), array('lol', 'lol'));
        $socket->expects($this->at(0))
            ->method('write')
            ->with("lol", "<".(LOG_AUTHPRIV + LOG_WARNING).">: ");
        $socket->expects($this->at(1))
            ->method('write')
            ->with("hej", "<".(LOG_AUTHPRIV + LOG_WARNING).">: ");

        $handler->setSocket($socket);

        $handler->handle($this->getRecordWithMessage("hej\nlol"));
    }

    protected function getRecordWithMessage($msg)
    {
        return array('message' => $msg, 'level' => ehough_epilog_Logger::WARNING, 'context' => null, 'extra' => array(), 'channel' => 'lol');
    }
}

<?php

class SyslogUdpHandlerTest extends PHPUnit_Framework_TestCase
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
        $handler = new ehough_epilog_handler_SyslogUdpHandler("127.0.0.1", 514, "local5");
        $handler->setFormatter(new ehough_epilog_formatter_ChromePHPFormatter());

        $socket = $this->getMock('ehough_epilog_handler_syslogudp_UdpSocket', array('write'), array('lol', 'lol'));
        $socket->expects($this->at(0))
            ->method('write')
            ->with("lol", "<172>: ");
        $socket->expects($this->at(1))
            ->method('write')
            ->with("hej", "<172>: ");

        $handler->setSocket($socket);

        $handler->handle($this->getRecordWithMessage("hej\nlol"));
    }

    protected function getRecordWithMessage($msg)
    {
        return array('message' => $msg, 'level' => ehough_epilog_Logger::WARNING, 'context' => null, 'extra' => array(), 'channel' => 'lol');
    }
}

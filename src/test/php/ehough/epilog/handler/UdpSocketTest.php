<?php

class UdpSocketTest extends ehough_epilog_TestCase
{
    public function setUp()
    {
        if (!function_exists('socket_create')) {

            $this->markTestSkipped('socket_create() not available');
            return;
        }
    }

    public function testWeDoNotSplitShortMessages()
    {
        $socket = $this->getMock('ehough_epilog_handler_syslogudp_UdpSocket', array('send'), array('lol', 'lol'));

        $socket->expects($this->at(0))
            ->method('send')
            ->with("HEADER: The quick brown fox jumps over the lazy dog");

        $socket->write("The quick brown fox jumps over the lazy dog", "HEADER: ");
    }

    public function testWeSplitLongMessages()
    {
        $socket = $this->getMock('ehough_epilog_handler_syslogudp_UdpSocket', array('send'), array('lol', 'lol'));

        $socket->expects($this->at(1))
            ->method('send')
            ->with("The quick brown fox jumps over the lazy dog");

        $aStringOfLength2048 = str_repeat("derp", 2048/4);

        $socket->write($aStringOfLength2048."The quick brown fox jumps over the lazy dog");
    }

    public function testAllSplitMessagesHasAHeader()
    {
        $socket = $this->getMock('ehough_epilog_handler_syslogudp_UdpSocket', array('send'), array('lol', 'lol'));

        $socket->expects($this->exactly(5))
            ->method('send')
            ->with($this->stringStartsWith("HEADER"));

        $aStringOfLength8192 = str_repeat("derp", 2048);

        $socket->write($aStringOfLength8192, "HEADER");
    }
}

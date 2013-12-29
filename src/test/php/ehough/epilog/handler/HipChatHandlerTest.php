<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * @author Rafael Dohms <rafael@doh.ms>
 * @see    https://www.hipchat.com/docs/api
 */
class ehough_epilog_handler_HipChatHandlerTest extends ehough_epilog_TestCase
{

    private $res;
    private $handler;

    public function setUp()
    {
        if (version_compare(PHP_VERSION, '5.3') < 0) {
            $this->markTestSkipped("PHP 5.2");
        }
    }


    public function testWriteHeader()
    {
        $this->createHandler();
        $this->handler->handle($this->getRecord(ehough_epilog_Logger::CRITICAL, 'test1'));
        fseek($this->res, 0);
        $content = fread($this->res, 1024);

        $this->assertRegexp('/POST \/v1\/rooms\/message\?format=json&auth_token=.* HTTP\/1.1\\r\\nHost: api.hipchat.com\\r\\nContent-Type: application\/x-www-form-urlencoded\\r\\nContent-Length: \d{2,4}\\r\\n\\r\\n/', $content);

        return $content;
    }

    /**
     * @depends testWriteHeader
     */
    public function testWriteContent($content)
    {
        $this->assertRegexp('/from=Monolog&room_id=room1&notify=0&message=test1&message_format=text&color=red$/', $content);
    }

    public function testWriteWithComplexMessage()
    {
        $this->createHandler();
        $this->handler->handle($this->getRecord(ehough_epilog_Logger::CRITICAL, 'Backup of database "example" finished in 16 minutes.'));
        fseek($this->res, 0);
        $content = fread($this->res, 1024);

        $this->assertRegexp('/message=Backup\+of\+database\+%22example%22\+finished\+in\+16\+minutes\./', $content);
    }

    /**
     * @dataProvider provideLevelColors
     */
    public function testWriteWithErrorLevelsAndColors($level, $expectedColor)
    {
        $this->createHandler();
        $this->handler->handle($this->getRecord($level, 'Backup of database "example" finished in 16 minutes.'));
        fseek($this->res, 0);
        $content = fread($this->res, 1024);

        $this->assertRegexp('/color='.$expectedColor.'/', $content);
    }

    public function provideLevelColors()
    {
        return array(
            array(ehough_epilog_Logger::DEBUG,    'gray'),
            array(ehough_epilog_Logger::INFO,     'green'),
            array(ehough_epilog_Logger::WARNING,  'yellow'),
            array(ehough_epilog_Logger::ERROR,    'red'),
            array(ehough_epilog_Logger::CRITICAL, 'red'),
            array(ehough_epilog_Logger::ALERT,    'red'),
            array(ehough_epilog_Logger::EMERGENCY,'red'),
            array(ehough_epilog_Logger::NOTICE,   'green'),
        );
    }

    /**
     * @dataProvider provideBatchRecords
     */
    public function testHandleBatch($records, $expectedColor)
    {
        $this->createHandler();

        $this->handler->handleBatch($records);

        fseek($this->res, 0);
        $content = fread($this->res, 1024);

        $this->assertRegexp('/color='.$expectedColor.'/', $content);
    }

    public function provideBatchRecords()
    {
        return array(
            array(
                array(
                    array('level' => ehough_epilog_Logger::WARNING, 'message' => 'Oh bugger!', 'level_name' => 'warning', 'datetime' => new DateTime()),
                    array('level' => ehough_epilog_Logger::NOTICE, 'message' => 'Something noticeable happened.', 'level_name' => 'notice', 'datetime' => new DateTime()),
                    array('level' => ehough_epilog_Logger::CRITICAL, 'message' => 'Everything is broken!', 'level_name' => 'critical', 'datetime' => new DateTime())
                ),
                'red',
            ),
            array(
                array(
                    array('level' => ehough_epilog_Logger::WARNING, 'message' => 'Oh bugger!', 'level_name' => 'warning', 'datetime' => new DateTime()),
                    array('level' => ehough_epilog_Logger::NOTICE, 'message' => 'Something noticeable happened.', 'level_name' => 'notice', 'datetime' => new DateTime()),
                ),
                'yellow',
            ),
            array(
                array(
                    array('level' => ehough_epilog_Logger::DEBUG, 'message' => 'Just debugging.', 'level_name' => 'debug', 'datetime' => new DateTime()),
                    array('level' => ehough_epilog_Logger::NOTICE, 'message' => 'Something noticeable happened.', 'level_name' => 'notice', 'datetime' => new DateTime()),
                ),
                'green',
            ),
            array(
                array(
                    array('level' => ehough_epilog_Logger::DEBUG, 'message' => 'Just debugging.', 'level_name' => 'debug', 'datetime' => new DateTime()),
                ),
                'gray',
            ),
        );
    }

    private function createHandler($token = 'myToken', $room = 'room1', $name = 'Monolog', $notify = false)
    {
        $constructorArgs = array($token, $room, $name, $notify, ehough_epilog_Logger::DEBUG);
        $this->res = fopen('php://memory', 'a');
        $this->handler = $this->getMock(
            'ehough_epilog_handler_HipChatHandler',
            array('fsockopen', 'streamSetTimeout', 'closeSocket'),
            $constructorArgs
        );

        $reflectionProperty = new ReflectionProperty('ehough_epilog_handler_SocketHandler', 'connectionString');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($this->handler, 'localhost:1234');

        $this->handler->expects($this->any())
            ->method('fsockopen')
            ->will($this->returnValue($this->res));
        $this->handler->expects($this->any())
            ->method('streamSetTimeout')
            ->will($this->returnValue(true));
        $this->handler->expects($this->any())
            ->method('closeSocket')
            ->will($this->returnValue(true));

        $this->handler->setFormatter($this->getIdentityFormatter());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testCreateWithTooLongName()
    {
        $hipChatHandler = new ehough_epilog_handler_HipChatHandler('token', 'room', 'SixteenCharsHere');
    }
}

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
 * @author Robert Kaufmann III <rok3@rok3.me>
 */
class LogEntriesHandlerTest extends ehough_epilog_TestCase
{
    /**
     * @var resource
     */
    private $res;

    /**
     * @var ehough_epilog_handler_LogEntriesHandler
     */
    private $handler;

    public function setup()
    {
        if (version_compare(PHP_VERSION, '5.3') < 0) {

            $this->markTestSkipped('PHP 5.2');
            return;
        }
    }

    public function testWriteContent()
    {
        $this->createHandler();
        $this->handler->handle($this->getRecord(ehough_epilog_Logger::CRITICAL, 'Critical write test'));

        fseek($this->res, 0);
        $content = fread($this->res, 1024);

        $this->assertRegexp('/testToken \[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\] test.CRITICAL: Critical write test/', $content);
    }

    public function testWriteBatchContent()
    {
        $records = array(
            $this->getRecord(),
            $this->getRecord(),
            $this->getRecord()
        );
        $this->createHandler();
        $this->handler->handleBatch($records);

        fseek($this->res, 0);
        $content = fread($this->res, 1024);

        $this->assertRegexp('/(testToken \[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\] .* \[\] \[\]\n){3}/', $content);
    }

    private function createHandler()
    {
        $useSSL = extension_loaded('openssl');
        $args = array('testToken', $useSSL, ehough_epilog_Logger::DEBUG, true);
        $this->res = fopen('php://memory', 'a');
        $this->handler = $this->getMock(
            'ehough_epilog_handler_LogEntriesHandler',
            array('fsockopen', 'streamSetTimeout', 'closeSocket'),
            $args
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
    }
}

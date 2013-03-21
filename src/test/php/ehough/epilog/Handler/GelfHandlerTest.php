<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//namespace Monolog\Handler;

//use Monolog\TestCase;
//use Monolog\Logger;
//use Monolog\Formatter\GelfMessageFormatter;

class GelfHandlerTest extends ehough_epilog_TestCase
{
    public function setUp()
    {
        if (!class_exists("Gelf\MessagePublisher") || !class_exists("Gelf\Message")) {
            $this->markTestSkipped("mlehner/gelf-php not installed");
        }

        require_once __DIR__ . '/GelfMocks.php';
    }

    /**
     * @covers ehough_epilog_handler_GelfHandler::__construct
     */
    public function testConstruct()
    {
        $handler = new ehough_epilog_handler_GelfHandler($this->getMessagePublisher());
        $this->assertInstanceOf('ehough_epilog_handler_GelfHandler', $handler);
    }

    protected function getHandler($messagePublisher)
    {
        $handler = new ehough_epilog_handler_GelfHandler($messagePublisher);

        return $handler;
    }

    protected function getMessagePublisher()
    {
        return new MockMessagePublisher('localhost');
    }

    public function testDebug()
    {
        $messagePublisher = $this->getMessagePublisher();
        $handler = $this->getHandler($messagePublisher);

        $record = $this->getRecord(ehough_epilog_Logger::DEBUG, "A test debug message");
        $handler->handle($record);

        $this->assertEquals(7, $messagePublisher->lastMessage->getLevel());
        $this->assertEquals('test', $messagePublisher->lastMessage->getFacility());
        $this->assertEquals($record['message'], $messagePublisher->lastMessage->getShortMessage());
        $this->assertEquals(null, $messagePublisher->lastMessage->getFullMessage());
    }

    public function testWarning()
    {
        $messagePublisher = $this->getMessagePublisher();
        $handler = $this->getHandler($messagePublisher);

        $record = $this->getRecord(ehough_epilog_Logger::WARNING, "A test warning message");
        $handler->handle($record);

        $this->assertEquals(4, $messagePublisher->lastMessage->getLevel());
        $this->assertEquals('test', $messagePublisher->lastMessage->getFacility());
        $this->assertEquals($record['message'], $messagePublisher->lastMessage->getShortMessage());
        $this->assertEquals(null, $messagePublisher->lastMessage->getFullMessage());
    }

    public function testInjectedGelfMessageFormatter()
    {
        $messagePublisher = $this->getMessagePublisher();
        $handler = $this->getHandler($messagePublisher);

        $handler->setFormatter(new ehough_epilog_formatter_GelfMessageFormatter('mysystem', 'EXT', 'CTX'));

        $record = $this->getRecord(ehough_epilog_Logger::WARNING, "A test warning message");
        $record['extra']['blarg'] = 'yep';
        $record['context']['from'] = 'logger';
        $handler->handle($record);

        $this->assertEquals('mysystem', $messagePublisher->lastMessage->getHost());
        $this->assertArrayHasKey('_EXTblarg', $messagePublisher->lastMessage->toArray());
        $this->assertArrayHasKey('_CTXfrom', $messagePublisher->lastMessage->toArray());
    }
}

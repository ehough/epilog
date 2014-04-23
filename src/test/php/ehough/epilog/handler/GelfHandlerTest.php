<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class ehough_epilog_handler_GelfHandlerTest extends ehough_epilog_TestCase
{
    public function setUp()
    {
        if (version_compare(PHP_VERSION, '5.3') < 0 || !class_exists("Gelf\MessagePublisher") || !class_exists("Gelf\Message")) {
            $this->markTestSkipped("graylog2/gelf-php not installed");
        }
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
        return $this->getMock('Gelf\Publisher', array('publish'), array(), '', false);
    }

    public function testDebug()
    {
        $record = $this->getRecord(ehough_epilog_Logger::DEBUG, "A test debug message");
        $expectedMessage = new Gelf\Message();
        $expectedMessage
            ->setLevel(7)
            ->setFacility("test")
            ->setShortMessage($record['message'])
            ->setTimestamp($record['datetime'])
        ;

        $messagePublisher = $this->getMessagePublisher();
        $messagePublisher->expects($this->once())
            ->method('publish')
            ->with($expectedMessage);

        $handler = $this->getHandler($messagePublisher);

        $handler->handle($record);

    }

    public function testWarning()
    {
        $record = $this->getRecord(ehough_epilog_Logger::WARNING, "A test warning message");
        $expectedMessage = new Gelf\Message();
        $expectedMessage
            ->setLevel(4)
            ->setFacility("test")
            ->setShortMessage($record['message'])
            ->setTimestamp($record['datetime'])
        ;

        $messagePublisher = $this->getMessagePublisher();
        $messagePublisher->expects($this->once())
            ->method('publish')
            ->with($expectedMessage);

        $handler = $this->getHandler($messagePublisher);

        $handler->handle($record);
    }

    public function testInjectedGelfMessageFormatter()
    {
        $record = $this->getRecord(ehough_epilog_Logger::WARNING, "A test warning message");
        $record['extra']['blarg'] = 'yep';
        $record['context']['from'] = 'logger';

        $expectedMessage = new Gelf\Message();
        $expectedMessage
            ->setLevel(4)
            ->setFacility("test")
            ->setHost("mysystem")
            ->setShortMessage($record['message'])
            ->setTimestamp($record['datetime'])
            ->setAdditional("EXTblarg", 'yep')
            ->setAdditional("CTXfrom", 'logger')
        ;

        $messagePublisher = $this->getMessagePublisher();
        $messagePublisher->expects($this->once())
            ->method('publish')
            ->with($expectedMessage);

        $handler = $this->getHandler($messagePublisher);
        $handler->setFormatter(new ehough_epilog_formatter_GelfMessageFormatter('mysystem', 'EXT', 'CTX'));
        $handler->handle($record);

    }
}

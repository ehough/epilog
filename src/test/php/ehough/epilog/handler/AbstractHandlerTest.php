<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class ehough_epilog_handler_AbstractHandlerTest extends ehough_epilog_TestCase
{
    /**
     * @covers ehough_epilog_handler_AbstractHandler::__construct
     * @covers ehough_epilog_handler_AbstractHandler::getLevel
     * @covers ehough_epilog_handler_AbstractHandler::setLevel
     * @covers ehough_epilog_handler_AbstractHandler::getBubble
     * @covers ehough_epilog_handler_AbstractHandler::setBubble
     * @covers ehough_epilog_handler_AbstractHandler::getFormatter
     * @covers ehough_epilog_handler_AbstractHandler::setFormatter
     */
    public function testConstructAndGetSet()
    {
        $handler = $this->getMockForAbstractClass('ehough_epilog_handler_AbstractHandler', array(ehough_epilog_Logger::WARNING, false));
        $this->assertEquals(ehough_epilog_Logger::WARNING, $handler->getLevel());
        $this->assertEquals(false, $handler->getBubble());

        $handler->setLevel(ehough_epilog_Logger::ERROR);
        $handler->setBubble(true);
        $handler->setFormatter($formatter = new ehough_epilog_formatter_LineFormatter);
        $this->assertEquals(ehough_epilog_Logger::ERROR, $handler->getLevel());
        $this->assertEquals(true, $handler->getBubble());
        $this->assertSame($formatter, $handler->getFormatter());
    }

    /**
     * @covers ehough_epilog_handler_AbstractHandler::handleBatch
     */
    public function testHandleBatch()
    {
        $handler = $this->getMockForAbstractClass('ehough_epilog_handler_AbstractHandler');
        $handler->expects($this->exactly(2))
            ->method('handle');
        $handler->handleBatch(array($this->getRecord(), $this->getRecord()));
    }

    /**
     * @covers ehough_epilog_handler_AbstractHandler::isHandling
     */
    public function testIsHandling()
    {
        $handler = $this->getMockForAbstractClass('ehough_epilog_handler_AbstractHandler', array(ehough_epilog_Logger::WARNING, false));
        $this->assertTrue($handler->isHandling($this->getRecord()));
        $this->assertFalse($handler->isHandling($this->getRecord(ehough_epilog_Logger::DEBUG)));
    }

    /**
     * @covers ehough_epilog_handler_AbstractHandler::getFormatter
     * @covers ehough_epilog_handler_AbstractHandler::getDefaultFormatter
     */
    public function testGetFormatterInitializesDefault()
    {
        $handler = $this->getMockForAbstractClass('ehough_epilog_handler_AbstractHandler');
        $this->assertInstanceOf('ehough_epilog_formatter_LineFormatter', $handler->getFormatter());
    }

    /**
     * @covers ehough_epilog_handler_AbstractHandler::pushProcessor
     * @covers ehough_epilog_handler_AbstractHandler::popProcessor
     * @expectedException LogicException
     */
    public function testPushPopProcessor()
    {
        $logger = $this->getMockForAbstractClass('ehough_epilog_handler_AbstractHandler');
        $processor1 = new ehough_epilog_processor_WebProcessor;
        $processor2 = new ehough_epilog_processor_WebProcessor;

        $logger->pushProcessor($processor1);
        $logger->pushProcessor($processor2);

        $this->assertEquals($processor2, $logger->popProcessor());
        $this->assertEquals($processor1, $logger->popProcessor());
        $logger->popProcessor();
    }

    /**
     * @covers ehough_epilog_handler_AbstractHandler::pushProcessor
     * @expectedException InvalidArgumentException
     */
    public function testPushProcessorWithNonCallable()
    {
        $handler = $this->getMockForAbstractClass('ehough_epilog_handler_AbstractHandler');

        $handler->pushProcessor(new stdClass());
    }
}

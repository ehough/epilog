<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//namespace Monolog;

//use Monolog\Processor\WebProcessor;
//use ehough_epilog_handler_TestHandler;

class ehough_epilog_LoggerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ehough_epilog_Logger::getName
     */
    public function testGetName()
    {
        $logger = new ehough_epilog_Logger('foo');
        $this->assertEquals('foo', $logger->getName());
    }

    /**
     * @covers ehough_epilog_Logger::getLevelName
     */
    public function testGetLevelName()
    {
        $this->assertEquals('ERROR', ehough_epilog_Logger::getLevelName(ehough_epilog_Logger::ERROR));
    }

    /**
     * @covers ehough_epilog_Logger::getLevelName
     * @expectedException InvalidArgumentException
     */
    public function testGetLevelNameThrows()
    {
        ehough_epilog_Logger::getLevelName(5);
    }

    /**
     * @covers ehough_epilog_Logger::__construct
     */
    public function testChannel()
    {
        $logger = new ehough_epilog_Logger('foo');
        $handler = new ehough_epilog_handler_TestHandler;
        $logger->pushHandler($handler);
        $logger->addWarning('test');
        list($record) = $handler->getRecords();
        $this->assertEquals('foo', $record['channel']);
    }

    /**
     * @covers ehough_epilog_Logger::addRecord
     */
    public function testLog()
    {
        $logger = new ehough_epilog_Logger(__METHOD__);

        $handler = $this->getMock('ehough_epilog_handler_NullHandler', array('handle'));
        $handler->expects($this->once())
            ->method('handle');
        $logger->pushHandler($handler);

        $this->assertTrue($logger->addWarning('test'));
    }

    /**
     * @covers ehough_epilog_Logger::addRecord
     */
    public function testLogNotHandled()
    {
        $logger = new ehough_epilog_Logger(__METHOD__);

        $handler = $this->getMock('ehough_epilog_handler_NullHandler', array('handle'), array(ehough_epilog_Logger::ERROR));
        $handler->expects($this->never())
            ->method('handle');
        $logger->pushHandler($handler);

        $this->assertFalse($logger->addWarning('test'));
    }

    public function testHandlersInCtor()
    {
        $handler1 = new ehough_epilog_handler_TestHandler;
        $handler2 = new ehough_epilog_handler_TestHandler;
        $logger = new ehough_epilog_Logger(__METHOD__, array($handler1, $handler2));

        $this->assertEquals($handler1, $logger->popHandler());
        $this->assertEquals($handler2, $logger->popHandler());
    }

    public function testProcessorsInCtor()
    {
        $processor1 = new ehough_epilog_processor_WebProcessor;
        $processor2 = new ehough_epilog_processor_WebProcessor;
        $logger = new ehough_epilog_Logger(__METHOD__, array(), array($processor1, $processor2));

        $this->assertEquals($processor1, $logger->popProcessor());
        $this->assertEquals($processor2, $logger->popProcessor());
    }

    /**
     * @covers ehough_epilog_Logger::pushHandler
     * @covers ehough_epilog_Logger::popHandler
     * @expectedException LogicException
     */
    public function testPushPopHandler()
    {
        $logger = new ehough_epilog_Logger(__METHOD__);
        $handler1 = new ehough_epilog_handler_TestHandler;
        $handler2 = new ehough_epilog_handler_TestHandler;

        $logger->pushHandler($handler1);
        $logger->pushHandler($handler2);

        $this->assertEquals($handler2, $logger->popHandler());
        $this->assertEquals($handler1, $logger->popHandler());
        $logger->popHandler();
    }

    /**
     * @covers ehough_epilog_Logger::pushProcessor
     * @covers ehough_epilog_Logger::popProcessor
     * @expectedException LogicException
     */
    public function testPushPopProcessor()
    {
        $logger = new ehough_epilog_Logger(__METHOD__);
        $processor1 = new ehough_epilog_processor_WebProcessor;
        $processor2 = new ehough_epilog_processor_WebProcessor;

        $logger->pushProcessor($processor1);
        $logger->pushProcessor($processor2);

        $this->assertEquals($processor2, $logger->popProcessor());
        $this->assertEquals($processor1, $logger->popProcessor());
        $logger->popProcessor();
    }

    /**
     * @covers ehough_epilog_Logger::pushProcessor
     * @expectedException InvalidArgumentException
     */
    public function testPushProcessorWithNonCallable()
    {
        $logger = new ehough_epilog_Logger(__METHOD__);

        $logger->pushProcessor(new stdClass());
    }

    /**
     * @covers ehough_epilog_Logger::addRecord
     */
    public function testProcessorsAreExecuted()
    {
        $logger = new ehough_epilog_Logger(__METHOD__);
        $handler = new ehough_epilog_handler_TestHandler;
        $logger->pushHandler($handler);
        $logger->pushProcessor(array($this, '_callbackTestProcessorsAreExecuted'));
        $logger->addError('test');
        list($record) = $handler->getRecords();
        $this->assertTrue($record['extra']['win']);
    }

    public function _callbackTestProcessorsAreExecuted($record)
    {
        $record['extra']['win'] = true;

        return $record;
    }

    /**
     * @covers ehough_epilog_Logger::addRecord
     */
    public function testProcessorsAreCalledOnlyOnce()
    {
        $logger = new ehough_epilog_Logger(__METHOD__);
        $handler = $this->getMock('ehough_epilog_handler_HandlerInterface');
        $handler->expects($this->any())
            ->method('isHandling')
            ->will($this->returnValue(true))
        ;
        $handler->expects($this->any())
            ->method('handle')
            ->will($this->returnValue(true))
        ;
        $logger->pushHandler($handler);

        $processor = $this->getMockBuilder('Monolog\Processor\WebProcessor')
            ->disableOriginalConstructor()
            ->setMethods(array('__invoke'))
            ->getMock()
        ;
        $processor->expects($this->once())
            ->method('__invoke')
            ->will($this->returnArgument(0))
        ;
        $logger->pushProcessor($processor);

        $logger->addError('test');
    }

    /**
     * @covers ehough_epilog_Logger::addRecord
     */
    public function testProcessorsNotCalledWhenNotHandled()
    {
        $logger = new ehough_epilog_Logger(__METHOD__);
        $handler = $this->getMock('ehough_epilog_handler_HandlerInterface');
        $handler->expects($this->once())
            ->method('isHandling')
            ->will($this->returnValue(false))
        ;
        $logger->pushHandler($handler);
        $logger->pushProcessor(array($this, '_callbackTestProcessorsNotCalledWhenNotHandled'));
        $logger->addAlert('test');
    }

    public function _callbackTestProcessorsNotCalledWhenNotHandled($record)
    {
        $this->fail('The processor should not be called');
    }

    /**
     * @covers ehough_epilog_Logger::addRecord
     */
    public function testHandlersNotCalledBeforeFirstHandling()
    {
        $logger = new ehough_epilog_Logger(__METHOD__);

        $handler1 = $this->getMock('ehough_epilog_handler_HandlerInterface');
        $handler1->expects($this->never())
            ->method('isHandling')
            ->will($this->returnValue(false))
        ;
        $handler1->expects($this->once())
            ->method('handle')
            ->will($this->returnValue(false))
        ;
        $logger->pushHandler($handler1);

        $handler2 = $this->getMock('ehough_epilog_handler_HandlerInterface');
        $handler2->expects($this->once())
            ->method('isHandling')
            ->will($this->returnValue(true))
        ;
        $handler2->expects($this->once())
            ->method('handle')
            ->will($this->returnValue(false))
        ;
        $logger->pushHandler($handler2);

        $handler3 = $this->getMock('ehough_epilog_handler_HandlerInterface');
        $handler3->expects($this->once())
            ->method('isHandling')
            ->will($this->returnValue(false))
        ;
        $handler3->expects($this->never())
            ->method('handle')
        ;
        $logger->pushHandler($handler3);

        $logger->debug('test');
    }

    /**
     * @covers ehough_epilog_Logger::addRecord
     */
    public function testBubblingWhenTheHandlerReturnsFalse()
    {
        $logger = new ehough_epilog_Logger(__METHOD__);

        $handler1 = $this->getMock('ehough_epilog_handler_HandlerInterface');
        $handler1->expects($this->any())
            ->method('isHandling')
            ->will($this->returnValue(true))
        ;
        $handler1->expects($this->once())
            ->method('handle')
            ->will($this->returnValue(false))
        ;
        $logger->pushHandler($handler1);

        $handler2 = $this->getMock('ehough_epilog_handler_HandlerInterface');
        $handler2->expects($this->any())
            ->method('isHandling')
            ->will($this->returnValue(true))
        ;
        $handler2->expects($this->once())
            ->method('handle')
            ->will($this->returnValue(false))
        ;
        $logger->pushHandler($handler2);

        $logger->debug('test');
    }

    /**
     * @covers ehough_epilog_Logger::addRecord
     */
    public function testNotBubblingWhenTheHandlerReturnsTrue()
    {
        $logger = new ehough_epilog_Logger(__METHOD__);

        $handler1 = $this->getMock('ehough_epilog_handler_HandlerInterface');
        $handler1->expects($this->any())
            ->method('isHandling')
            ->will($this->returnValue(true))
        ;
        $handler1->expects($this->never())
            ->method('handle')
        ;
        $logger->pushHandler($handler1);

        $handler2 = $this->getMock('ehough_epilog_handler_HandlerInterface');
        $handler2->expects($this->any())
            ->method('isHandling')
            ->will($this->returnValue(true))
        ;
        $handler2->expects($this->once())
            ->method('handle')
            ->will($this->returnValue(true))
        ;
        $logger->pushHandler($handler2);

        $logger->debug('test');
    }

    /**
     * @covers ehough_epilog_Logger::isHandling
     */
    public function testIsHandling()
    {
        $logger = new ehough_epilog_Logger(__METHOD__);

        $handler1 = $this->getMock('ehough_epilog_handler_HandlerInterface');
        $handler1->expects($this->any())
            ->method('isHandling')
            ->will($this->returnValue(false))
        ;

        $logger->pushHandler($handler1);
        $this->assertFalse($logger->isHandling(ehough_epilog_Logger::DEBUG));

        $handler2 = $this->getMock('ehough_epilog_handler_HandlerInterface');
        $handler2->expects($this->any())
            ->method('isHandling')
            ->will($this->returnValue(true))
        ;

        $logger->pushHandler($handler2);
        $this->assertTrue($logger->isHandling(ehough_epilog_Logger::DEBUG));
    }

    /**
     * @dataProvider logMethodProvider
     * @covers ehough_epilog_Logger::addDebug
     * @covers ehough_epilog_Logger::addInfo
     * @covers ehough_epilog_Logger::addNotice
     * @covers ehough_epilog_Logger::addWarning
     * @covers ehough_epilog_Logger::addError
     * @covers ehough_epilog_Logger::addCritical
     * @covers ehough_epilog_Logger::addAlert
     * @covers ehough_epilog_Logger::addEmergency
     * @covers ehough_epilog_Logger::debug
     * @covers ehough_epilog_Logger::info
     * @covers ehough_epilog_Logger::notice
     * @covers ehough_epilog_Logger::warn
     * @covers ehough_epilog_Logger::err
     * @covers ehough_epilog_Logger::crit
     * @covers ehough_epilog_Logger::alert
     * @covers ehough_epilog_Logger::emerg
     */
    public function testLogMethods($method, $expectedLevel)
    {
        $logger = new ehough_epilog_Logger('foo');
        $handler = new ehough_epilog_handler_TestHandler;
        $logger->pushHandler($handler);
        $logger->{$method}('test');
        list($record) = $handler->getRecords();
        $this->assertEquals($expectedLevel, $record['level']);
    }

    public function logMethodProvider()
    {
        return array(
            // monolog methods
            array('addDebug',     ehough_epilog_Logger::DEBUG),
            array('addInfo',      ehough_epilog_Logger::INFO),
            array('addNotice',    ehough_epilog_Logger::NOTICE),
            array('addWarning',   ehough_epilog_Logger::WARNING),
            array('addError',     ehough_epilog_Logger::ERROR),
            array('addCritical',  ehough_epilog_Logger::CRITICAL),
            array('addAlert',     ehough_epilog_Logger::ALERT),
            array('addEmergency', ehough_epilog_Logger::EMERGENCY),

            // ZF/Sf2 compat methods
            array('debug',  ehough_epilog_Logger::DEBUG),
            array('info',   ehough_epilog_Logger::INFO),
            array('notice', ehough_epilog_Logger::NOTICE),
            array('warn',   ehough_epilog_Logger::WARNING),
            array('err',    ehough_epilog_Logger::ERROR),
            array('crit',   ehough_epilog_Logger::CRITICAL),
            array('alert',  ehough_epilog_Logger::ALERT),
            array('emerg',  ehough_epilog_Logger::EMERGENCY),
        );
    }
}

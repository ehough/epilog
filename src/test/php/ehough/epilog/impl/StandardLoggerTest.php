<?php
/**
 * Copyright 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of epilog (https://github.com/ehough/epilog)
 *
 * epilog is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * epilog is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

require_once __DIR__ . '/../../../../resources/fixtures/handler/TestHandler.php';
require_once __DIR__ . '/../../../../resources/fixtures/processor/TestSuccessProcessor.php';
require_once __DIR__ . '/../../../../resources/fixtures/processor/TestFailProcessor.php';

/**
 * Original author...
 *
 * Copyright (c) Jordi Boggiano
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

class ehough_epilog_impl_LoggerTest extends PHPUnit_Framework_TestCase
{
    public function testGetName()
    {
        $logger = new ehough_epilog_impl_StandardLogger('foo');
        $this->assertEquals('foo', $logger->getName());
    }

    /**
     * @covers Monolog\Logger::__construct
     */
    public function testChannel()
    {
        $logger = new ehough_epilog_impl_StandardLogger('foo');
        $handler = new ehough_epilog_impl_handler_TestHandler();
        $logger->pushHandler($handler);
        $logger->warn('test');
        list($record) = $handler->getRecords();
        $this->assertEquals('foo', $record['channel']);
    }

    public function testLog()
    {
        $logger = new ehough_epilog_impl_StandardLogger(__METHOD__);

        $handler = $this->getMock('ehough_epilog_impl_handler_NullHandler', array('handle'));
        $handler->expects($this->once())
            ->method('handle');
        $logger->pushHandler($handler);

        $this->assertTrue($logger->warn('test'));
    }

    public function testLogNotHandled()
    {
        $logger = new ehough_epilog_impl_StandardLogger(__METHOD__);

        $handler = $this->getMock('ehough_epilog_impl_handler_NullHandler', array('handle'), array(ehough_epilog_api_ILogger::ERROR));
        $handler->expects($this->never())
            ->method('handle');
        $logger->pushHandler($handler);

        $this->assertFalse($logger->warn('test'));
    }

    /**
     * @expectedException LogicException
     */
    public function testPushPopHandler()
    {
        $logger = new ehough_epilog_impl_StandardLogger(__METHOD__);
        $handler1 = new ehough_epilog_impl_handler_TestHandler();
        $handler2 = new ehough_epilog_impl_handler_TestHandler();

        $logger->pushHandler($handler1);
        $logger->pushHandler($handler2);

        $this->assertEquals($handler2, $logger->popHandler());
        $this->assertEquals($handler1, $logger->popHandler());
        $logger->popHandler();
    }

    /**
     * @expectedException LogicException
     */
    public function testPushPopProcessor()
    {
        $logger = new ehough_epilog_impl_StandardLogger(__METHOD__);
        $processor1 = new ehough_epilog_impl_processor_WebProcessor;
        $processor2 = new ehough_epilog_impl_processor_WebProcessor;

        $logger->pushProcessor($processor1);
        $logger->pushProcessor($processor2);

        $this->assertEquals($processor2, $logger->popProcessor());
        $this->assertEquals($processor1, $logger->popProcessor());
        $logger->popProcessor();
    }

    public function testProcessorsAreExecuted()
    {
        $logger = new ehough_epilog_impl_StandardLogger(__METHOD__);
        $handler = new ehough_epilog_impl_handler_TestHandler();
        $logger->pushHandler($handler);
        $logger->pushProcessor(new ehough_epilog_impl_processor_TestSuccessProcessor());
        $logger->error('test');
        list($record) = $handler->getRecords();
        $this->assertTrue($record['extra']['win']);
    }

    public function testProcessorsAreCalledOnlyOnce()
    {
        $logger = new ehough_epilog_impl_StandardLogger(__METHOD__);
        $handler = $this->getMock('ehough_epilog_api_IHandler');
        $handler->expects($this->any())
            ->method('isHandling')
            ->will($this->returnValue(true))
        ;
        $handler->expects($this->any())
            ->method('handle')
            ->will($this->returnValue(true))
        ;
        $logger->pushHandler($handler);

        $processor = $this->getMockBuilder('ehough_epilog_impl_processor_WebProcessor')
            ->disableOriginalConstructor()
            ->setMethods(array('process'))
            ->getMock()
        ;
        $processor->expects($this->once())
            ->method('process')
            ->will($this->returnArgument(0))
        ;
        $logger->pushProcessor($processor);

        $logger->error('test');
    }

    public function testProcessorsNotCalledWhenNotHandled()
    {
        $logger = new ehough_epilog_impl_StandardLogger(__METHOD__);
        $handler = $this->getMock('ehough_epilog_api_IHandler');
        $handler->expects($this->once())
            ->method('isHandling')
            ->will($this->returnValue(false))
        ;
        $logger->pushHandler($handler);
        $logger->pushProcessor(new ehough_epilog_impl_processor_TestFailProcessor($this));
        $logger->critical('test');
    }

    public function testHandlersNotCalledBeforeFirstHandling()
    {
        $logger = new ehough_epilog_impl_StandardLogger(__METHOD__);

        $handler1 = $this->getMock('ehough_epilog_api_IHandler');
        $handler1->expects($this->never())
            ->method('isHandling')
            ->will($this->returnValue(false))
        ;
        $handler1->expects($this->once())
            ->method('handle')
            ->will($this->returnValue(false))
        ;
        $logger->pushHandler($handler1);

        $handler2 = $this->getMock('ehough_epilog_api_IHandler');
        $handler2->expects($this->once())
            ->method('isHandling')
            ->will($this->returnValue(true))
        ;
        $handler2->expects($this->once())
            ->method('handle')
            ->will($this->returnValue(false))
        ;
        $logger->pushHandler($handler2);

        $handler3 = $this->getMock('ehough_epilog_api_IHandler');
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

    public function testBubblingWhenTheHandlerReturnsFalse()
    {
        $logger = new ehough_epilog_impl_StandardLogger(__METHOD__);

        $handler1 = $this->getMock('ehough_epilog_api_IHandler');
        $handler1->expects($this->any())
            ->method('isHandling')
            ->will($this->returnValue(true))
        ;
        $handler1->expects($this->once())
            ->method('handle')
            ->will($this->returnValue(false))
        ;
        $logger->pushHandler($handler1);

        $handler2 = $this->getMock('ehough_epilog_api_IHandler');
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

    public function testNotBubblingWhenTheHandlerReturnsTrue()
    {
        $logger = new ehough_epilog_impl_StandardLogger(__METHOD__);

        $handler1 = $this->getMock('ehough_epilog_api_IHandler');
        $handler1->expects($this->any())
            ->method('isHandling')
            ->will($this->returnValue(true))
        ;
        $handler1->expects($this->never())
            ->method('handle')
        ;
        $logger->pushHandler($handler1);

        $handler2 = $this->getMock('ehough_epilog_api_IHandler');
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

    public function testIsHandling()
    {
        $logger = new ehough_epilog_impl_StandardLogger(__METHOD__);

        $handler1 = $this->getMock('ehough_epilog_api_IHandler');
        $handler1->expects($this->any())
            ->method('isHandling')
            ->will($this->returnValue(false))
        ;

        $logger->pushHandler($handler1);
        $this->assertFalse($logger->isDebugEnabled());

        $handler2 = $this->getMock('ehough_epilog_api_IHandler');
        $handler2->expects($this->any())
            ->method('isHandling')
            ->will($this->returnValue(true))
        ;

        $logger->pushHandler($handler2);
        $this->assertTrue($logger->isDebugEnabled());
    }

    /**
     * @dataProvider logMethodProvider
     */
    public function testLogMethods($method, $expectedLevel)
    {
        $logger = new ehough_epilog_impl_StandardLogger('foo');
        $handler = new ehough_epilog_impl_handler_TestHandler();
        $logger->pushHandler($handler);
        $logger->{$method}('test');
        list($record) = $handler->getRecords();
        $this->assertEquals($expectedLevel, $record['level']);
    }

    public function logMethodProvider()
    {
        return array(

            // ZF/Sf2 compat methods
            array('debug',  ehough_epilog_api_ILogger::DEBUG),
            array('info',   ehough_epilog_api_ILogger::INFO),
            array('warn',   ehough_epilog_api_ILogger::WARNING),
            array('error',    ehough_epilog_api_ILogger::ERROR),
            array('critical',   ehough_epilog_api_ILogger::CRITICAL),
        );
    }
}
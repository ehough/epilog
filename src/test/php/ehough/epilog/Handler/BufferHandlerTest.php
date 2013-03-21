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

class BufferHandlerTest extends ehough_epilog_TestCase
{
    /**
     * @covers ehough_epilog_handler_BufferHandler::__construct
     * @covers ehough_epilog_handler_BufferHandler::handle
     * @covers ehough_epilog_handler_BufferHandler::close
     */
    public function testHandleBuffers()
    {
        $test = new ehough_epilog_handler_TestHandler();
        $handler = new ehough_epilog_handler_BufferHandler($test);
        $handler->handle($this->getRecord(ehough_epilog_Logger::DEBUG));
        $handler->handle($this->getRecord(ehough_epilog_Logger::INFO));
        $this->assertFalse($test->hasDebugRecords());
        $this->assertFalse($test->hasInfoRecords());
        $handler->close();
        $this->assertTrue($test->hasInfoRecords());
        $this->assertTrue(count($test->getRecords()) === 2);
    }

    /**
     * @covers ehough_epilog_handler_BufferHandler::close
     * @covers ehough_epilog_handler_BufferHandler::flush
     */
    public function testDestructPropagatesRecords()
    {
        $test = new ehough_epilog_handler_TestHandler();
        $handler = new ehough_epilog_handler_BufferHandler($test);
        $handler->handle($this->getRecord(ehough_epilog_Logger::WARNING));
        $handler->handle($this->getRecord(ehough_epilog_Logger::DEBUG));
        $handler->__destruct();
        $this->assertTrue($test->hasWarningRecords());
        $this->assertTrue($test->hasDebugRecords());
    }

    /**
     * @covers ehough_epilog_handler_BufferHandler::handle
     */
    public function testHandleBufferLimit()
    {
        $test = new ehough_epilog_handler_TestHandler();
        $handler = new ehough_epilog_handler_BufferHandler($test, 2);
        $handler->handle($this->getRecord(ehough_epilog_Logger::DEBUG));
        $handler->handle($this->getRecord(ehough_epilog_Logger::DEBUG));
        $handler->handle($this->getRecord(ehough_epilog_Logger::INFO));
        $handler->handle($this->getRecord(ehough_epilog_Logger::WARNING));
        $handler->close();
        $this->assertTrue($test->hasWarningRecords());
        $this->assertTrue($test->hasInfoRecords());
        $this->assertFalse($test->hasDebugRecords());
    }

    /**
     * @covers ehough_epilog_handler_BufferHandler::handle
     */
    public function testHandleBufferLimitWithFlushOnOverflow()
    {
        $test = new ehough_epilog_handler_TestHandler();
        $handler = new ehough_epilog_handler_BufferHandler($test, 3, ehough_epilog_Logger::DEBUG, true, true);

        // send two records
        $handler->handle($this->getRecord(ehough_epilog_Logger::DEBUG));
        $handler->handle($this->getRecord(ehough_epilog_Logger::DEBUG));
        $handler->handle($this->getRecord(ehough_epilog_Logger::DEBUG));
        $this->assertFalse($test->hasDebugRecords());
        $this->assertCount(0, $test->getRecords());

        // overflow
        $handler->handle($this->getRecord(ehough_epilog_Logger::INFO));
        $this->assertTrue($test->hasDebugRecords());
        $this->assertCount(3, $test->getRecords());

        // should buffer again
        $handler->handle($this->getRecord(ehough_epilog_Logger::WARNING));
        $this->assertCount(3, $test->getRecords());

        $handler->close();
        $this->assertCount(5, $test->getRecords());
        $this->assertTrue($test->hasWarningRecords());
        $this->assertTrue($test->hasInfoRecords());
    }

    /**
     * @covers ehough_epilog_handler_BufferHandler::handle
     */
    public function testHandleLevel()
    {
        $test = new ehough_epilog_handler_TestHandler();
        $handler = new ehough_epilog_handler_BufferHandler($test, 0, ehough_epilog_Logger::INFO);
        $handler->handle($this->getRecord(ehough_epilog_Logger::DEBUG));
        $handler->handle($this->getRecord(ehough_epilog_Logger::INFO));
        $handler->handle($this->getRecord(ehough_epilog_Logger::WARNING));
        $handler->handle($this->getRecord(ehough_epilog_Logger::DEBUG));
        $handler->close();
        $this->assertTrue($test->hasWarningRecords());
        $this->assertTrue($test->hasInfoRecords());
        $this->assertFalse($test->hasDebugRecords());
    }

    /**
     * @covers ehough_epilog_handler_BufferHandler::flush
     */
    public function testFlush()
    {
        $test = new ehough_epilog_handler_TestHandler();
        $handler = new ehough_epilog_handler_BufferHandler($test, 0);
        $handler->handle($this->getRecord(ehough_epilog_Logger::DEBUG));
        $handler->handle($this->getRecord(ehough_epilog_Logger::INFO));
        $handler->flush();
        $this->assertTrue($test->hasInfoRecords());
        $this->assertTrue($test->hasDebugRecords());
        $this->assertFalse($test->hasWarningRecords());
    }

    /**
     * @covers ehough_epilog_handler_BufferHandler::handle
     */
    public function testHandleUsesProcessors()
    {
        $test = new ehough_epilog_handler_TestHandler();
        $handler = new ehough_epilog_handler_BufferHandler($test);
        $handler->pushProcessor(function ($record) {
            $record['extra']['foo'] = true;

            return $record;
        });
        $handler->handle($this->getRecord(ehough_epilog_Logger::WARNING));
        $handler->flush();
        $this->assertTrue($test->hasWarningRecords());
        $records = $test->getRecords();
        $this->assertTrue($records[0]['extra']['foo']);
    }
}

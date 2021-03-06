<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class ehough_epilog_handler_AbstractProcessingHandlerTest extends ehough_epilog_TestCase
{
    private $_handledRecord;

    /**
     * @covers ehough_epilog_handler_AbstractProcessingHandler::handle
     */
    public function testHandleLowerLevelMessage()
    {
        $handler = $this->getMockForAbstractClass('ehough_epilog_handler_AbstractProcessingHandler', array(ehough_epilog_Logger::WARNING, true));
        $this->assertFalse($handler->handle($this->getRecord(ehough_epilog_Logger::DEBUG)));
    }

    /**
     * @covers ehough_epilog_handler_AbstractProcessingHandler::handle
     */
    public function testHandleBubbling()
    {
        $handler = $this->getMockForAbstractClass('ehough_epilog_handler_AbstractProcessingHandler', array(ehough_epilog_Logger::DEBUG, true));
        $this->assertFalse($handler->handle($this->getRecord()));
    }

    /**
     * @covers ehough_epilog_handler_AbstractProcessingHandler::handle
     */
    public function testHandleNotBubbling()
    {
        $handler = $this->getMockForAbstractClass('ehough_epilog_handler_AbstractProcessingHandler', array(ehough_epilog_Logger::DEBUG, false));
        $this->assertTrue($handler->handle($this->getRecord()));
    }

    /**
     * @covers ehough_epilog_handler_AbstractProcessingHandler::handle
     */
    public function testHandleIsFalseWhenNotHandled()
    {
        $handler = $this->getMockForAbstractClass('ehough_epilog_handler_AbstractProcessingHandler', array(ehough_epilog_Logger::WARNING, false));
        $this->assertTrue($handler->handle($this->getRecord()));
        $this->assertFalse($handler->handle($this->getRecord(ehough_epilog_Logger::DEBUG)));
    }

    /**
     * @covers ehough_epilog_handler_AbstractProcessingHandler::processRecord
     */
    public function testProcessRecord()
    {
        $handler = $this->getMockForAbstractClass('ehough_epilog_handler_AbstractProcessingHandler');
        $handler->pushProcessor(new ehough_epilog_processor_WebProcessor(array(
            'REQUEST_URI' => '',
            'REQUEST_METHOD' => '',
            'REMOTE_ADDR' => '',
            'SERVER_NAME' => '',
            'UNIQUE_ID' => '',
        )));
        $this->_handledRecord = null;
        $handler->expects($this->once())
            ->method('write')
            ->will($this->returnCallback(array($this, '_callbackTestProcessRecord')))
        ;
        $handler->handle($this->getRecord());
        $this->assertEquals(6, count($this->_handledRecord['extra']));
    }

    public function _callbackTestProcessRecord($record)
    {
        $this->_handledRecord = $record;
    }
}

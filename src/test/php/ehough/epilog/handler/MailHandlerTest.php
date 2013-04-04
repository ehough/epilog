<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class ehough_epilog_handler_MailHandlerTest extends ehough_epilog_TestCase
{
    /**
     * @covers ehough_epilog_handler_MailHandler::handleBatch
     */
    public function testHandleBatch()
    {
        $formatter = $this->getMock('ehough_epilog_formatter_FormatterInterface');
        $formatter->expects($this->once())
            ->method('formatBatch'); // Each record is formatted

        $handler = $this->getMockForAbstractClass('ehough_epilog_handler_MailHandler');
        $handler->expects($this->once())
            ->method('send');
        $handler->expects($this->never())
            ->method('write'); // write is for individual records

        $handler->setFormatter($formatter);

        $handler->handleBatch($this->getMultipleRecords());
    }

    /**
     * @covers ehough_epilog_handler_MailHandler::handleBatch
     */
    public function testHandleBatchNotSendsMailIfMessagesAreBelowLevel()
    {
        $records = array(
            $this->getRecord(ehough_epilog_Logger::DEBUG, 'debug message 1'),
            $this->getRecord(ehough_epilog_Logger::DEBUG, 'debug message 2'),
            $this->getRecord(ehough_epilog_Logger::INFO, 'information'),
        );

        $handler = $this->getMockForAbstractClass('ehough_epilog_handler_MailHandler');
        $handler->expects($this->never())
            ->method('send');
        $handler->setLevel(ehough_epilog_Logger::ERROR);

        $handler->handleBatch($records);
    }

    /**
     * @covers ehough_epilog_handler_MailHandler::write
     */
    public function testHandle()
    {
        $handler = $this->getMockForAbstractClass('ehough_epilog_handler_MailHandler');

        $record = $this->getRecord();
        $records = array($record);
        $records[0]['formatted'] = '['.$record['datetime']->format('Y-m-d H:i:s').'] test.WARNING: test [] []'."\n";

        $handler->expects($this->once())
            ->method('send')
            ->with($records[0]['formatted'], $records);

        $handler->handle($record);
    }
}

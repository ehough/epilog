<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class ehough_epilog_handler_RavenHandlerTest extends ehough_epilog_TestCase
{
    public function setUp()
    {
        if (!class_exists("Raven_Client")) {
            $this->markTestSkipped("raven/raven not installed");
        }

        require_once dirname(__FILE__) . '/MockRavenClient.php';
    }

    /**
     * @covers ehough_epilog_handler_RavenHandler::__construct
     */
    public function testConstruct()
    {
        $handler = new ehough_epilog_handler_RavenHandler($this->getRavenClient());
        $this->assertInstanceOf('ehough_epilog_handler_RavenHandler', $handler);
    }

    protected function getHandler($ravenClient)
    {
        $handler = new ehough_epilog_handler_RavenHandler($ravenClient);
        return $handler;
    }

    protected function getRavenClient()
    {
        $dsn = 'http://43f6017361224d098402974103bfc53d:a6a0538fc2934ba2bed32e08741b2cd3@marca.python.live.cheggnet.com:9000/1';
        return new MockRavenClient($dsn);
    }

    public function testDebug()
    {
        $ravenClient = $this->getRavenClient();
        $handler = $this->getHandler($ravenClient);

        $record = $this->getRecord(ehough_epilog_Logger::DEBUG, "A test debug message");
        $handler->handle($record);

        $this->assertEquals(MockRavenClient::DEBUG, $ravenClient->lastData['level']);
        $this->assertContains($record['message'], $ravenClient->lastData['message']);
    }

    public function testWarning()
    {
        $ravenClient = $this->getRavenClient();
        $handler = $this->getHandler($ravenClient);

        $record = $this->getRecord(ehough_epilog_Logger::WARNING, "A test warning message");
        $handler->handle($record);

        $this->assertEquals(MockRavenClient::WARNING, $ravenClient->lastData['level']);
        $this->assertContains($record['message'], $ravenClient->lastData['message']);
    }
    
    public function testTag()
    {
        $ravenClient = $this->getRavenClient();
        $handler = $this->getHandler($ravenClient);

        $tags = array(1, 2, 'foo');
        $record = $this->getRecord(ehough_epilog_Logger::INFO, "test", array('tags' => $tags));
        $handler->handle($record);

        $this->assertEquals($tags, $ravenClient->lastData['tags']);
    }

    public function testException()
    {
        $ravenClient = $this->getRavenClient();
        $handler = $this->getHandler($ravenClient);

        try {
            $this->methodThatThrowsAnException();
        } catch (Exception $e) {
            $record = $this->getRecord(ehough_epilog_Logger::ERROR, $e->getMessage(), array('exception' => $e));
            $handler->handle($record);
        }

        $this->assertEquals($record['message'], $ravenClient->lastData['message']);
    }

    public function testHandleBatch()
    {
        $records = $this->getMultipleRecords();
        $records[] = $this->getRecord(ehough_epilog_Logger::WARNING, 'warning');
        $records[] = $this->getRecord(ehough_epilog_Logger::WARNING, 'warning');

        $logFormatter = $this->getMock('ehough_epilog_formatter_FormatterInterface');
        $logFormatter->expects($this->once())->method('formatBatch');

        $formatter = $this->getMock('ehough_epilog_formatter_FormatterInterface');
        $formatter->expects($this->once())->method('format')->with($this->callback(array($this, '__callbackTestHandleBatch')));

        $handler = $this->getHandler($this->getRavenClient());
        $handler->setBatchFormatter($logFormatter);
        $handler->setFormatter($formatter);
        $handler->handleBatch($records);
    }

    public function __callbackTestHandleBatch($record)
    {
        return $record['level'] == 400;
    }

    public function testHandleBatchDoNothingIfRecordsAreBelowLevel()
    {
        $records = array(
            $this->getRecord(ehough_epilog_Logger::DEBUG, 'debug message 1'),
            $this->getRecord(ehough_epilog_Logger::DEBUG, 'debug message 2'),
            $this->getRecord(ehough_epilog_Logger::INFO, 'information'),
        );

        $handler = $this->getMock('ehough_epilog_handler_RavenHandler', null, array($this->getRavenClient()));
        $handler->expects($this->never())->method('handle');
        $handler->setLevel(ehough_epilog_Logger::ERROR);
        $handler->handleBatch($records);
    }

    public function testGetSetBatchFormatter()
    {
        $ravenClient = $this->getRavenClient();
        $handler = $this->getHandler($ravenClient);

        $handler->setBatchFormatter($formatter = new ehough_epilog_formatter_LineFormatter());
        $this->assertSame($formatter, $handler->getBatchFormatter());
    }

    private function methodThatThrowsAnException()
    {
        throw new Exception('This is an exception');
    }
}

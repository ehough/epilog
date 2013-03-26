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
//use Monolog\Handler\RavenHandler;

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

        $this->assertEquals($ravenClient::DEBUG, $ravenClient->lastData['level']);
        $this->assertContains($record['message'], $ravenClient->lastData['message']);
    }

    public function testWarning()
    {
        $ravenClient = $this->getRavenClient();
        $handler = $this->getHandler($ravenClient);

        $record = $this->getRecord(ehough_epilog_Logger::WARNING, "A test warning message");
        $handler->handle($record);

        $this->assertEquals($ravenClient::WARNING, $ravenClient->lastData['level']);
        $this->assertContains($record['message'], $ravenClient->lastData['message']);
    }

    public function testException()
    {
        $ravenClient = $this->getRavenClient();
        $handler = $this->getHandler($ravenClient);

        try {
            $this->methodThatThrowsAnException();
        } catch (\Exception $e) {
            $record = $this->getRecord(ehough_epilog_Logger::ERROR, $e->getMessage(), array('exception' => $e));
            $handler->handle($record);
        }

        $this->assertEquals($record['message'], $ravenClient->lastData['message']);
    }

    private function methodThatThrowsAnException()
    {
        throw new \Exception('This is an exception');
    }
}

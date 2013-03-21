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

class ehough_epilog_handler_StreamHandlerTest extends ehough_epilog_TestCase
{
    /**
     * @covers ehough_epilog_handler_StreamHandler::__construct
     * @covers ehough_epilog_handler_StreamHandler::write
     */
    public function testWrite()
    {
        $handle = fopen('php://memory', 'a+');
        $handler = new ehough_epilog_handler_StreamHandler($handle);
        $handler->setFormatter($this->getIdentityFormatter());
        $handler->handle($this->getRecord(ehough_epilog_Logger::WARNING, 'test'));
        $handler->handle($this->getRecord(ehough_epilog_Logger::WARNING, 'test2'));
        $handler->handle($this->getRecord(ehough_epilog_Logger::WARNING, 'test3'));
        fseek($handle, 0);
        $this->assertEquals('testtest2test3', fread($handle, 100));
    }

    /**
     * @covers ehough_epilog_handler_StreamHandler::close
     */
    public function testClose()
    {
        $handle = fopen('php://memory', 'a+');
        $handler = new ehough_epilog_handler_StreamHandler($handle);
        $this->assertTrue(is_resource($handle));
        $handler->close();
        $this->assertFalse(is_resource($handle));
    }

    /**
     * @covers ehough_epilog_handler_StreamHandler::write
     */
    public function testWriteCreatesTheStreamResource()
    {
        $handler = new ehough_epilog_handler_StreamHandler('php://memory');
        $handler->handle($this->getRecord());
    }

    /**
     * @expectedException LogicException
     * @covers ehough_epilog_handler_StreamHandler::__construct
     * @covers ehough_epilog_handler_StreamHandler::write
     */
    public function testWriteMissingResource()
    {
        $handler = new ehough_epilog_handler_StreamHandler(null);
        $handler->handle($this->getRecord());
    }

    /**
     * @expectedException UnexpectedValueException
     * @covers ehough_epilog_handler_StreamHandler::__construct
     * @covers ehough_epilog_handler_StreamHandler::write
     */
    public function testWriteInvalidResource()
    {
        $handler = new ehough_epilog_handler_StreamHandler('bogus://url');
        $handler->handle($this->getRecord());
    }

    /**
     * @expectedException UnexpectedValueException
     * @covers ehough_epilog_handler_StreamHandler::__construct
     * @covers ehough_epilog_handler_StreamHandler::write
     */
    public function testWriteNonExistingResource()
    {
        $handler = new ehough_epilog_handler_StreamHandler('/foo/bar/baz/'.rand(0, 10000));
        $handler->handle($this->getRecord());
    }
}

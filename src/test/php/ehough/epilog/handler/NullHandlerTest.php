<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * @covers ehough_epilog_handler_NullHandler::handle
 */
class ehough_epilog_handler_NullHandlerTest extends ehough_epilog_TestCase
{
    public function testHandle()
    {
        $handler = new ehough_epilog_handler_NullHandler();
        $this->assertTrue($handler->handle($this->getRecord()));
    }

    public function testHandleLowerLevelRecord()
    {
        $handler = new ehough_epilog_handler_NullHandler(ehough_epilog_Logger::WARNING);
        $this->assertFalse($handler->handle($this->getRecord(ehough_epilog_Logger::DEBUG)));
    }
}

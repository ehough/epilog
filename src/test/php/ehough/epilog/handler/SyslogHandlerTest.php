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
//use Monolog\Logger;

class ehough_epilog_handler_SyslogHandlerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ehough_epilog_handler_SyslogHandler::__construct
     */
    public function testConstruct()
    {
        $handler = new ehough_epilog_handler_SyslogHandler('test');
        $this->assertInstanceOf('ehough_epilog_handler_SyslogHandler', $handler);

        $handler = new ehough_epilog_handler_SyslogHandler('test', LOG_USER);
        $this->assertInstanceOf('ehough_epilog_handler_SyslogHandler', $handler);

        $handler = new ehough_epilog_handler_SyslogHandler('test', 'user');
        $this->assertInstanceOf('ehough_epilog_handler_SyslogHandler', $handler);

        $handler = new ehough_epilog_handler_SyslogHandler('test', LOG_USER, ehough_epilog_Logger::DEBUG, true, LOG_PERROR);
        $this->assertInstanceOf('ehough_epilog_handler_SyslogHandler', $handler);
    }

    /**
     * @covers ehough_epilog_handler_SyslogHandler::__construct
     */
    public function testConstructInvalidFacility()
    {
        $this->setExpectedException('UnexpectedValueException');
        $handler = new ehough_epilog_handler_SyslogHandler('test', 'unknown');
    }
}

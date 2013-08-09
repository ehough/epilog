<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class ehough_epilog_ErrorHandlerTest extends PHPUnit_Framework_TestCase
{
    public function testHandleError()
    {
        $logger = new ehough_epilog_Logger('test', array($handler = new ehough_epilog_handler_TestHandler()));
        $errHandler = new ehough_epilog_ErrorHandler($logger);

        $errHandler->registerErrorHandler(array(E_USER_NOTICE => ehough_epilog_Logger::EMERGENCY), false);
        trigger_error('Foo', E_USER_ERROR);
        $this->assertCount(1, $handler->getRecords());
        $this->assertTrue($handler->hasErrorRecords());
        trigger_error('Foo', E_USER_NOTICE);
        $this->assertCount(2, $handler->getRecords());
        $this->assertTrue($handler->hasEmergencyRecords());
    }
}

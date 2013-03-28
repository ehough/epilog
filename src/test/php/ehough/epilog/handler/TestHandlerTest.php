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
 * @covers ehough_epilog_handler_TestHandler
 */
class ehough_epilog_handler_TestHandlerTest extends ehough_epilog_TestCase
{
    /**
     * @dataProvider methodProvider
     */
    public function testHandler($method, $level)
    {
        $handler = new ehough_epilog_handler_TestHandler;
        $record = $this->getRecord($level, 'test'.$method);
        $this->assertFalse($handler->{'has'.$method}($record));
        $this->assertFalse($handler->{'has'.$method.'Records'}());
        $handler->handle($record);

        $this->assertFalse($handler->{'has'.$method}('bar'));
        $this->assertTrue($handler->{'has'.$method}($record));
        $this->assertTrue($handler->{'has'.$method}('test'.$method));
        $this->assertTrue($handler->{'has'.$method.'Records'}());

        $records = $handler->getRecords();
        unset($records[0]['formatted']);
        $this->assertEquals(array($record), $records);
    }

    public function methodProvider()
    {
        return array(
            array('Emergency', ehough_epilog_Logger::EMERGENCY),
            array('Alert'    , ehough_epilog_Logger::ALERT),
            array('Critical' , ehough_epilog_Logger::CRITICAL),
            array('Error'    , ehough_epilog_Logger::ERROR),
            array('Warning'  , ehough_epilog_Logger::WARNING),
            array('Info'     , ehough_epilog_Logger::INFO),
            array('Notice'   , ehough_epilog_Logger::NOTICE),
            array('Debug'    , ehough_epilog_Logger::DEBUG),
        );
    }
}

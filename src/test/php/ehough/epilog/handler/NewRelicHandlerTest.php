<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class ehough_epilog_handler_NewRelicHandlerTest extends ehough_epilog_TestCase
{
    public static $appname;

    public function setUp()
    {
        self::$appname = null;
    }

    /**
     * @expectedException ehough_epilog_handler_MissingExtensionException
     */
    public function testThehandlerThrowsAnExceptionIfTheNRExtensionIsNotLoaded()
    {
        $handler = new StubNewRelicHandlerWithoutExtension();
        $handler->handle($this->getRecord(ehough_epilog_Logger::ERROR));
    }

    public function testThehandlerCanHandleTheRecord()
    {
        $handler = new StubNewRelicHandler();
        $handler->handle($this->getRecord(ehough_epilog_Logger::ERROR));
    }

    public function testThehandlerCanAddParamsToTheNewRelicTrace()
    {
        $handler = new StubNewRelicHandler();
        $handler->handle($this->getRecord(ehough_epilog_Logger::ERROR, 'log message', array('a' => 'b')));
    }

    public function testTheAppNameIsNullByDefault()
    {
        $handler = new StubNewRelicHandler();
        $handler->handle($this->getRecord(ehough_epilog_Logger::ERROR, 'log message'));

        $this->assertEquals(null, self::$appname);
    }

    public function testTheAppNameCanBeInjectedFromtheConstructor()
    {
        $handler = new StubNewRelicHandler(ehough_epilog_psr_LogLevel::ALERT, false, 'myAppName');
        $handler->handle($this->getRecord(ehough_epilog_Logger::ERROR, 'log message'));

        $this->assertEquals('myAppName', self::$appname);
    }

    public function testTheAppNameCanBeOverriddenFromEachLog()
    {
        $handler = new StubNewRelicHandler(ehough_epilog_psr_LogLevel::ALERT, false, 'myAppName');
        $handler->handle($this->getRecord(ehough_epilog_Logger::ERROR, 'log message', array('appname' => 'logAppName')));

        $this->assertEquals('logAppName', self::$appname);
    }
}

class StubNewRelicHandlerWithoutExtension extends ehough_epilog_handler_NewRelicHandler
{
    protected function isNewRelicEnabled()
    {
        return false;
    }
}

class StubNewRelicHandler extends ehough_epilog_handler_NewRelicHandler
{
    protected function isNewRelicEnabled()
    {
        return true;
    }
}

function newrelic_notice_error()
{
    return true;
}

function newrelic_set_appname($appname)
{
    return ehough_epilog_handler_NewRelicHandlerTest::$appname = $appname;
}

function newrelic_add_custom_parameter()
{
    return true;
}

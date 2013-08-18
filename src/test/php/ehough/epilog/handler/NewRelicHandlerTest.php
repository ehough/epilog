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

function newrelic_add_custom_parameter()
{
    return true;
}

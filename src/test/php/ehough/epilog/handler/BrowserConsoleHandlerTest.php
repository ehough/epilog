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
 * @covers ehough_epilog_handler_BrowserConsoleHandler
 */
class BrowserConsoleHandlerTest extends ehough_epilog_TestCase
{
    protected function setUp()
    {
        ehough_epilog_handler_BrowserConsoleHandler::reset();
    }

    protected function generateScript()
    {
        $reflMethod = new ReflectionMethod('ehough_epilog_handler_BrowserConsoleHandler', 'generateScript');
        $reflMethod->setAccessible(true);

        return $reflMethod->invoke(null);
    }

    public function testStyling()
    {
        if (version_compare(PHP_VERSION, '5.3') < 0) {

            $this->markTestSkipped('PHP < 5.3');
            return;
        }

        $handler = new ehough_epilog_handler_BrowserConsoleHandler();
        $handler->setFormatter($this->getIdentityFormatter());

        $handler->handle($this->getRecord(ehough_epilog_Logger::DEBUG, 'foo[[bar]]{color: red}'));

        $expected = <<<EOF
(function (c) {if (c && c.groupCollapsed) {
c.log("%cfoo%cbar%c", "font-weight: normal", "color: red", "font-weight: normal");
}})(console);
EOF;

        $this->assertEquals($expected, $this->generateScript());
    }

    public function testEscaping()
    {
        $handler = new ehough_epilog_handler_BrowserConsoleHandler();
        $handler->setFormatter($this->getIdentityFormatter());

        $handler->handle($this->getRecord(ehough_epilog_Logger::DEBUG, "[foo] [[\"bar\n[baz]\"]]{color: red}"));

        $expected = <<<EOF
(function (c) {if (c && c.groupCollapsed) {
c.log("%c[foo] %c\"bar\\n[baz]\"%c", "font-weight: normal", "color: red", "font-weight: normal");
}})(console);
EOF;

        $this->assertEquals($expected, $this->generateScript());
    }


    public function testAutolabel()
    {
        $handler = new ehough_epilog_handler_BrowserConsoleHandler();
        $handler->setFormatter($this->getIdentityFormatter());

        $handler->handle($this->getRecord(ehough_epilog_Logger::DEBUG, '[[foo]]{macro: autolabel}'));
        $handler->handle($this->getRecord(ehough_epilog_Logger::DEBUG, '[[bar]]{macro: autolabel}'));
        $handler->handle($this->getRecord(ehough_epilog_Logger::DEBUG, '[[foo]]{macro: autolabel}'));

        $expected = <<<EOF
(function (c) {if (c && c.groupCollapsed) {
c.log("%c%cfoo%c", "font-weight: normal", "background-color: blue; color: white; border-radius: 3px; padding: 0 2px 0 2px", "font-weight: normal");
c.log("%c%cbar%c", "font-weight: normal", "background-color: green; color: white; border-radius: 3px; padding: 0 2px 0 2px", "font-weight: normal");
c.log("%c%cfoo%c", "font-weight: normal", "background-color: blue; color: white; border-radius: 3px; padding: 0 2px 0 2px", "font-weight: normal");
}})(console);
EOF;

        $this->assertEquals($expected, $this->generateScript());
    }

    public function testContext()
    {
        $handler = new ehough_epilog_handler_BrowserConsoleHandler();
        $handler->setFormatter($this->getIdentityFormatter());

        $handler->handle($this->getRecord(ehough_epilog_Logger::DEBUG, 'test', array('foo' => 'bar')));

        $expected = <<<EOF
(function (c) {if (c && c.groupCollapsed) {
c.groupCollapsed("%ctest", "font-weight: normal");
c.log("%c%s", "font-weight: bold", "Context");
c.log("%s: %o", "foo", "bar");
c.groupEnd();
}})(console);
EOF;

        $this->assertEquals($expected, $this->generateScript());
    }

    public function testConcurrentHandlers()
    {
        $handler1 = new ehough_epilog_handler_BrowserConsoleHandler();
        $handler1->setFormatter($this->getIdentityFormatter());

        $handler2 = new ehough_epilog_handler_BrowserConsoleHandler();
        $handler2->setFormatter($this->getIdentityFormatter());

        $handler1->handle($this->getRecord(ehough_epilog_Logger::DEBUG, 'test1'));
        $handler2->handle($this->getRecord(ehough_epilog_Logger::DEBUG, 'test2'));
        $handler1->handle($this->getRecord(ehough_epilog_Logger::DEBUG, 'test3'));
        $handler2->handle($this->getRecord(ehough_epilog_Logger::DEBUG, 'test4'));

        $expected = <<<EOF
(function (c) {if (c && c.groupCollapsed) {
c.log("%ctest1", "font-weight: normal");
c.log("%ctest2", "font-weight: normal");
c.log("%ctest3", "font-weight: normal");
c.log("%ctest4", "font-weight: normal");
}})(console);
EOF;

        $this->assertEquals($expected, $this->generateScript());
    }
}

<?php
/*
 * This file is part of the epilog package.
 *
 * (c) Eric Hough <ehough.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class ehough_epilog_LoggerFactoryTest extends PHPUnit_Framework_TestCase
{
    public function testReuseLoggers()
    {
        $logger = ehough_epilog_LoggerFactory::getLogger('fake');
        $logger2 = ehough_epilog_LoggerFactory::getLogger('fake');

        $this->assertSame($logger, $logger2);
    }

    public function testNoHandlers()
    {
        $logger = ehough_epilog_LoggerFactory::getLogger('fake');

        $this->assertTrue($logger->getName() === 'fake');
        $this->assertTrue($logger instanceof ehough_epilog_Logger);

        $handler = $logger->popHandler();

        $this->assertTrue($handler instanceof ehough_epilog_handler_NullHandler);
    }

    public function testSetHandlers()
    {
        ehough_epilog_LoggerFactory::setHandlerStack(array(new ehough_epilog_handler_NullHandler()));
        ehough_epilog_LoggerFactory::setProcessorStack(array(new ehough_epilog_processor_MemoryUsageProcessor()));

        $logger = ehough_epilog_LoggerFactory::getLogger('fake2');

        $this->assertTrue($logger->getName() === 'fake2');
        $this->assertTrue($logger instanceof ehough_epilog_Logger);

        $handler = $logger->popHandler();

        $this->assertTrue($handler instanceof ehough_epilog_handler_NullHandler);

        $proc = $logger->popProcessor();

        $this->assertTrue($proc instanceof ehough_epilog_processor_MemoryUsageProcessor);
    }
}
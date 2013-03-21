<?php
/**
 * Copyright 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of epilog (https://github.com/ehough/epilog)
 *
 * epilog is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * epilog is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

class ehough_epilog_impl_LoggerFactoryTest extends PHPUnit_Framework_TestCase
{
    public function testReuseLoggers()
    {
        $logger = ehough_epilog_api_LoggerFactory::getLogger('fake');
        $logger2 = ehough_epilog_api_LoggerFactory::getLogger('fake');

        $this->assertSame($logger, $logger2);
    }

    public function testNoHandlers()
    {
        $logger = ehough_epilog_api_LoggerFactory::getLogger('fake');

        $this->assertTrue($logger->getName() === 'fake');
        $this->assertTrue($logger instanceof ehough_epilog_Logger);

        $handler = $logger->popHandler();

        $this->assertTrue($handler instanceof ehough_epilog_handler_NullHandler);
    }

    public function testSetHandlers()
    {
        ehough_epilog_api_LoggerFactory::setHandlerStack(array(new ehough_epilog_handler_NullHandler()));
        ehough_epilog_api_LoggerFactory::setProcessorStack(array(new ehough_epilog_processor_MemoryUsageProcessor()));

        $logger = ehough_epilog_api_LoggerFactory::getLogger('fake2');

        $this->assertTrue($logger->getName() === 'fake2');
        $this->assertTrue($logger instanceof ehough_epilog_Logger);

        $handler = $logger->popHandler();

        $this->assertTrue($handler instanceof ehough_epilog_handler_NullHandler);

        $proc = $logger->popProcessor();

        $this->assertTrue($proc instanceof ehough_epilog_processor_MemoryUsageProcessor);
    }
}
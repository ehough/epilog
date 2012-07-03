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

/**
 * Original author...
 *
 * Copyright (c) Jordi Boggiano
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

require_once __DIR__ . '/../../../../../resources/fixtures/TestCase.php';

class ehough_epilog_impl_handler_StreamHandlerTest extends ehough_epilog_impl_TestCase
{
    public function testWrite()
    {
        $handle = fopen('php://memory', 'a+');
        $handler = new ehough_epilog_impl_handler_StreamHandler($handle);
        $handler->setFormatter($this->getIdentityFormatter());
        $handler->handle($this->getRecord(ehough_epilog_api_ILogger::WARNING, 'test'));
        $handler->handle($this->getRecord(ehough_epilog_api_ILogger::WARNING, 'test2'));
        $handler->handle($this->getRecord(ehough_epilog_api_ILogger::WARNING, 'test3'));
        fseek($handle, 0);
        $this->assertEquals('testtest2test3', fread($handle, 100));
    }

    public function testClose()
    {
        $handle = fopen('php://memory', 'a+');
        $handler = new ehough_epilog_impl_handler_StreamHandler($handle);
        $this->assertTrue(is_resource($handle));
        $handler->close();
        $this->assertFalse(is_resource($handle));
    }

    public function testWriteCreatesTheStreamResource()
    {
        $handler = new ehough_epilog_impl_handler_StreamHandler('php://memory');
        $handler->handle($this->getRecord());
        $this->assertTrue(true);
    }

    /**
     * @expectedException LogicException
     */
    public function testWriteMissingResource()
    {
        $handler = new ehough_epilog_impl_handler_StreamHandler(null);
        $handler->handle($this->getRecord());
    }

    /**
     * @expectedException UnexpectedValueException
     */
    public function testWriteInvalidResource()
    {
        $handler = new ehough_epilog_impl_handler_StreamHandler('bogus://url');
        $handler->handle($this->getRecord());
    }
}
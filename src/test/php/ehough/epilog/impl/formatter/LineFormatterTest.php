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

class ehough_epilog_impl_formatter_LineFormatterTest extends PHPUnit_Framework_TestCase
{
    public function testDefFormatWithString()
    {
        $formatter = new ehough_epilog_impl_formatter_LineFormatter(null, 'Y-m-d');
        $message = $formatter->format(array(
            'level_name' => 'WARNING',
            'channel' => 'log',
            'context' => array(),
            'message' => 'foo',
            'time' => microtime(true),
            'extra' => array(),
        ));
        $this->assertEquals('['.date('Y-m-d').'] log.WARNING: foo array () array ()'."\n", $message);
    }

    public function testDefFormatWithArrayContext()
    {
        $formatter = new ehough_epilog_impl_formatter_LineFormatter(null, 'Y-m-d');
        $message = $formatter->format(array(
            'level_name' => 'ERROR',
            'channel' => 'meh',
            'message' => 'foo',
            'time' => microtime(true),
            'extra' => array(),
            'context' => array(
                'foo' => 'bar',
                'baz' => 'qux',
            )
        ));
        $this->assertEquals('['.date('Y-m-d').'] meh.ERROR: foo array (  \'foo\' => \'bar\',  \'baz\' => \'qux\',) array ()'."\n", $message);
    }

    public function testDefFormatExtras()
    {
        $formatter = new ehough_epilog_impl_formatter_LineFormatter(null, 'Y-m-d');
        $message = $formatter->format(array(
            'level_name' => 'ERROR',
            'channel' => 'meh',
            'context' => array(),
            'time' => microtime(true),
            'extra' => array('ip' => '127.0.0.1'),
            'message' => 'log',
        ));
        $this->assertEquals('['.date('Y-m-d').'] meh.ERROR: log array () array (  \'ip\' => \'127.0.0.1\',)'."\n", $message);
    }

    public function testFormatExtras()
    {
        $formatter = new ehough_epilog_impl_formatter_LineFormatter("[%time%] %channel%.%level_name%: %message% %context% %extra.file% %extra%\n", 'Y-m-d');
        $message = $formatter->format(array(
            'level_name' => 'ERROR',
            'channel' => 'meh',
            'context' => array(),
            'time' => microtime(true),
            'extra' => array('ip' => '127.0.0.1', 'file' => 'test'),
            'message' => 'log',
        ));
        $this->assertEquals('['.date('Y-m-d').'] meh.ERROR: log array () test array (  \'ip\' => \'127.0.0.1\',)'."\n", $message);
    }

    public function testDefFormatWithObject()
    {
        $formatter = new ehough_epilog_impl_formatter_LineFormatter(null, 'Y-m-d');
        $message = $formatter->format(array(
            'level_name' => 'ERROR',
            'channel' => 'meh',
            'context' => array(),
            'time' => microtime(true),
            'extra' => array('foo' => new TestFoo, 'bar' => new TestBar, 'baz' => array(), 'res' => fopen('php://memory', 'rb')),
            'message' => 'foobar',
        ));
        if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
            $this->assertEquals('['.date('Y-m-d').'] meh.ERROR: foobar array () array (  \'foo\' => \'[object] (TestFoo: {\"foo\":\"foo\',)","bar\' => \'[object] (Monolog\Formatter\TestBar: {})","baz":array (),"res\' => \'[resource]\',)'."\n", $message);
        } else {
            $this->assertEquals('['.date('Y-m-d').'] meh.ERROR: foobar array () array (  \'foo\' => \'[object] (TestFoo: TestFoo::__set_state(array(   \'foo\' => \'foo\',)))\',  \'bar\' => \'[object] (TestBar: TestBar::__set_state(array()))\',  \'baz\' =>   array (  ),  \'res\' => \'[resource]\',)'."\n", $message);
        }
    }

    public function testBatchFormat()
    {
        $formatter = new ehough_epilog_impl_formatter_LineFormatter(null, 'Y-m-d');
        $message = $formatter->formatBatch(array(
            array(
                'level_name' => 'CRITICAL',
                'channel' => 'test',
                'message' => 'bar',
                'context' => array(),
                'time' => microtime(true),
                'extra' => array(),
            ),
            array(
                'level_name' => 'WARNING',
                'channel' => 'log',
                'message' => 'foo',
                'context' => array(),
                'time' => microtime(true),
                'extra' => array(),
            ),
        ));
        $this->assertEquals('['.date('Y-m-d').'] test.CRITICAL: bar array () array ()'."\n".'['.date('Y-m-d').'] log.WARNING: foo array () array ()'."\n", $message);
    }
}

class TestFoo
{
    public $foo = 'foo';
}

class TestBar
{
    public function __toString()
    {
        return 'bar';
    }
}
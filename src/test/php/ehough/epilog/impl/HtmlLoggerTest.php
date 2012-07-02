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

class ehough_epilog_impl_HtmlLoggerTest extends PHPUnit_Framework_TestCase {

    private $_sut;

    public function setup()
    {
        $this->_sut = new ehough_epilog_impl_HtmlLogger();
    }

    public function testLogNoArgs()
    {
        $this->expectOutputRegex($this->_regex('hello', 'hello!'));

        $this->_sut->setEnabled(true);
        $this->_sut->log('hello', 'hello!');
    }

    public function testLogOneArg()
    {
        $this->expectOutputRegex($this->_regex('boo', 'hello, eric!'));

        $this->_sut->setEnabled(true);
        $this->_sut->log('boo', 'hello, %s!', 'eric');
    }

    public function testLogTwoArgs()
    {
        $this->expectOutputRegex($this->_regex('boo', 'hello, eric! you have earned 12'));

        $this->_sut->setEnabled(true);
        $this->_sut->log('boo', 'hello, %s! you have earned %d', 'eric', 12);
    }

    public function testLogDisabled()
    {
        $this->_sut->setEnabled(false);
        $this->_sut->log('hello', 'hello!');
        $this->assertTrue(true);
    }

    private function _regex($prefix, $message)
    {
        return '/<div><tt style="font-size: small">[0-9]{1,3}\.[0-9]{2} ms \(' . $prefix . '\) ' . $message . ' \(memory: (?:[1-9],)?[0-9]{3} KB\)<\/tt><\/div>/';
    }
}
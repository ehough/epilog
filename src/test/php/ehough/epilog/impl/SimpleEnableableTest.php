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

class ehough_epilog_impl_SimpleEnableableTest extends PHPUnit_Framework_TestCase {

    private $_sut;

    public $_onEnableCalled;
    public $_onDisableCalled;

    public function setup()
    {
        $this->_sut = new ehough_epilog_impl_SimpleEnableable();

        $this->_onEnableCalled = false;
        $this->_onDisableCalled = false;
    }

    public function testToggle()
    {
        $this->assertFalse($this->_sut->isEnabled());

        $this->_sut->setEnabled(true);

        $this->assertTrue($this->_sut->isEnabled());

        $this->_sut->setEnabled(false);

        $this->assertFalse($this->_sut->isEnabled());
    }
}
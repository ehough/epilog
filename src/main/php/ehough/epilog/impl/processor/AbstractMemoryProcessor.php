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

/**
 * Some methods that are common for all memory processors
 *
 * @author Rob Jensen
 */
abstract class ehough_epilog_impl_processor_AbstractMemoryProcessor implements ehough_epilog_api_IProcessor
{
    private $_realUsage;

    /**
     * Constructor.
     *
     * @param boolean $realUsage Whether to use real memory usage or not.
     */
    public function __construct($realUsage = true)
    {
        $this->_realUsage = (boolean) $realUsage;
    }

    /**
     * Formats byte count into a human readable string.
     *
     * @param integer $bytes The number of bytes.
     *
     * @return string The string representation of the byte count.
     */
    protected final function _formatBytes($bytes)
    {
        $bytes = (int) $bytes;

        if ($bytes > (1024 * 1024)) {

            return round((($bytes / 1024) / 1024), 2) .' MB';

        }

        if ($bytes > 1024) {

            return round(($bytes / 1024), 2) .' KB';
        }

        return $bytes . ' B';
    }

    /**
     * Determines if we're processing real memory usage.
     *
     * @return bool True if this is processing real memory usage, false otherwise.
     */
    protected final function _isProcessingRealUsage()
    {
        return $this->_realUsage;
    }
}
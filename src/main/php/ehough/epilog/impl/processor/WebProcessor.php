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
 * Injects url/method and remote IP of the current web request in all records.
 *
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
class ehough_epilog_impl_processor_WebProcessor implements ehough_epilog_api_IProcessor
{
    private $_serverData;

    /**
     * Constructor.
     *
     * @param mixed $serverData Array or object with ArrayAccess that provides access to the $_SERVER data.
     *
     * @throws UnexpectedValueException If ServerData is not accessible.
     */
    public function __construct($serverData = null)
    {
        if (null === $serverData) {

            $this->_serverData =& $_SERVER;

        } else if (is_array($serverData) || $serverData instanceof ArrayAccess) {

            $this->_serverData = $serverData;

        } else {

            throw new UnexpectedValueException('$serverData must be an array or object implementing ArrayAccess.');
        }
    }

    /**
     * Process a single record.
     *
     * @param array $record The log record to process.
     *
     * @return array The processed record.
     */
    public function process(array $record)
    {
        // skip processing if for some reason request data
        // is not present (CLI or wonky SAPIs)
        if (!isset($this->_serverData['REQUEST_URI'])) {

            return $record;
        }

        if (!isset($this->_serverData['HTTP_REFERER'])) {

            $this->_serverData['HTTP_REFERER'] = null;
        }

        $record['extra'] = array_merge(
            $record['extra'],
            array(
             'url'         => $this->_serverData['REQUEST_URI'],
             'ip'          => $this->_serverData['REMOTE_ADDR'],
             'http_method' => $this->_serverData['REQUEST_METHOD'],
             'server'      => $this->_serverData['SERVER_NAME'],
             'referrer'    => $this->_serverData['HTTP_REFERER'],
            )
        );

        return $record;
    }
}
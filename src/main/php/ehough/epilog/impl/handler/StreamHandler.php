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
 * Stores to any stream resource
 *
 * Can be used to store into php://stderr, remote and local files, etc.
 *
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
final class ehough_epilog_impl_handler_StreamHandler extends ehough_epilog_impl_handler_AbstractProcessingHandler
{
    private $_stream;

    private $_url;

    /**
     * Constructor.
     *
     * @param string          $stream The stream.
     * @param boolean|integer $level  The minimum logging level at which this handler will be triggered.
     * @param boolean         $bubble Whether the messages that are handled can bubble up the stack or not.
     */
    public function __construct($stream, $level = ehough_epilog_api_ILogger::DEBUG, $bubble = true)
    {
        parent::__construct($level, $bubble);

        if (is_resource($stream)) {

            $this->_stream = $stream;

        } else {

            $this->_url = $stream;
        }
    }

    /**
     * Closes the handler.
     *
     * This will be called automatically when the object is destroyed
     *
     * @return void
     */
    public function close()
    {
        if (is_resource($this->_stream)) {

            fclose($this->_stream);
        }

        $this->_stream = null;
    }

    /**
     * Write the record down to the log of the implementing handler.
     *
     * @param array $record The log record to write.
     *
     * @return void
     *
     * @throws LogicException If the stream cannot be closed.
     * @throws UnexpectedValueException If the stream is invalid.
     */
    protected function write(array $record)
    {
        if ($this->_stream === null) {

            if (!$this->_url) {

                throw new LogicException(
                    'Missing stream url, the stream can not be opened. This may be caused by a premature call to close().'
                );
            }

            $this->_stream = @fopen($this->_url, 'a');

            if (!is_resource($this->_stream)) {

                $this->_stream = null;

                throw new UnexpectedValueException(sprintf('The stream or file "%s" could not be opened; it may be invalid or not writable.', $this->_url));
            }
        }

        fwrite($this->_stream, (string) $record['formatted']);
    }
}
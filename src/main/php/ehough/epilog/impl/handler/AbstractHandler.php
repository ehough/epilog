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
 * Base Handler class providing the Handler structure.
 *
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
abstract class ehough_epilog_impl_handler_AbstractHandler implements ehough_epilog_api_IHandler
{
    private $_level = ehough_epilog_api_ILogger::DEBUG;

    private $_bubble = false;

    private $_formatter = null;

    private $_processors = array();

    /**
     * Constructor.
     *
     * @param integer $level        The minimum logging level at which this handler will be triggered.
     * @param boolean $shouldBubble Whether the messages that are handled can bubble up the stack or not.
     */
    public function __construct($level = ehough_epilog_api_ILogger::DEBUG, $shouldBubble = true)
    {
        $this->_level  = $level;
        $this->_bubble = $shouldBubble;
    }

    /**
     * Checks whether the given record will be handled by this handler.
     *
     * This is mostly done for performance reasons, to avoid calling processors for nothing.
     *
     * @param array $record Records to check for.
     *
     * @return bool True if this handler is handling this record, false otherwise.
     */
    public final function isHandling(array $record)
    {
        return $record['level'] >= $this->_level;
    }

    /**
     * Handles a set of records at once.
     *
     * @param array $records The records to handle (an array of record arrays).
     *
     * @return void
     */
    public final function handleBatch(array $records)
    {
        foreach ($records as $record) {

            $this->handle($record);
        }
    }

    /**
     * Adds a processor in the stack.
     *
     * @param ehough_epilog_api_IProcessor $callback The processor to push.
     *
     * @return void
     */
    public final function pushProcessor(ehough_epilog_api_IProcessor $callback)
    {
        array_unshift($this->_processors, $callback);
    }

    /**
     * Removes the processor on top of the stack and returns it.
     *
     * @return ehough_epilog_api_IProcessor
     *
     * @throws LogicException If the processor stack is currently empty.
     */
    public final function popProcessor()
    {
        if (count($this->_processors) === 0) {

            throw new LogicException('You tried to pop from an empty processor stack.');
        }

        return array_shift($this->_processors);
    }

    /**
     * Sets the formatter.
     *
     * @param ehough_epilog_api_IFormatter $formatter The new formatter.
     *
     * @return void
     */
    public final function setFormatter(ehough_epilog_api_IFormatter $formatter)
    {
        $this->_formatter = $formatter;
    }

    /**
     * Gets the formatter.
     *
     * @return ehough_epilog_api_IFormatter
     */
    public final function getFormatter()
    {
        if ($this->_formatter === null) {

            $this->_formatter = $this->getDefaultFormatter();
        }

        return $this->_formatter;
    }

    /**
     * Sets minimum logging level at which this handler will be triggered.
     *
     * @param integer $level The minimum logging level at which this handler will be triggered.
     *
     * @return void
     */
    public final function setLevel($level)
    {
        $this->_level = $level;
    }

    /**
     * Gets minimum logging level at which this handler will be triggered.
     *
     * @return integer The minimum logging level at which this handler will be triggered.
     */
    public final function getLevel()
    {
        return $this->_level;
    }

    /**
     * Sets the bubbling behavior.
     *
     * @param boolean $shouldBubble True means that bubbling is not permitted.
     *                        False means that this handler allows bubbling.
     *
     * @return void
     */
    public final function setShouldBubble($shouldBubble)
    {
        $this->_bubble = (boolean) $shouldBubble;
    }

    /**
     * Gets the bubbling behavior.
     *
     * @return boolean True means that bubbling is not permitted.
     *                 False means that this handler allows bubbling.
     */
    public final function getShouldBubble()
    {
        return $this->_bubble;
    }

    /**
     * Destructor.
     */
    public final function __destruct()
    {
        try {

            $this->close();

        } catch (Exception $e) {

            return;
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
        //override point
    }

    /**
     * Gets the default formatter.
     *
     * @return ehough_epilog_api_IFormatter
     */
    protected final function getDefaultFormatter()
    {
        return new ehough_epilog_impl_formatter_LineFormatter();
    }

    /**
     * Get the stack of processors for this handler (may be empty).
     *
     * @return mixed The stack of processors for this handler (may be empty).
     */
    protected final function getProcessors()
    {
        return $this->_processors;
    }
}
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
 * Base Handler class providing the Handler structure
 *
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
abstract class ehough_epilog_impl_handler_AbstractHandler implements ehough_epilog_api_IHandler
{
    private $level = ehough_epilog_api_ILogger::DEBUG;

    private $bubble = false;

    private $formatter;

    private $processors = array();

    /**
     * @param integer $level  The minimum logging level at which this handler will be triggered
     * @param Boolean $bubble Whether the messages that are handled can bubble up the stack or not
     */
    public function __construct($level = ehough_epilog_api_ILogger::DEBUG, $bubble = true)
    {
        $this->level = $level;
        $this->bubble = $bubble;
    }

    /**
     * {@inheritdoc}
     */
    public final function isHandling(array $record)
    {
        return $record['level'] >= $this->level;
    }

    /**
     * {@inheritdoc}
     */
    public final function handleBatch(array $records)
    {
        foreach ($records as $record) {

            $this->handle($record);
        }
    }

    /**
     * {@inheritdoc}
     */
    public final function pushProcessor(ehough_epilog_api_IProcessor $callback)
    {
        if (! is_callable($callback)) {

            throw new InvalidArgumentException('Processors must be valid callables (callback or object with an __invoke method), '.var_export($callback, true).' given');
        }

        array_unshift($this->processors, $callback);
    }

    /**
     * {@inheritdoc}
     */
    public final function popProcessor()
    {
        if (! $this->processors) {

            throw new LogicException('You tried to pop from an empty processor stack.');
        }

        return array_shift($this->processors);
    }

    /**
     * {@inheritdoc}
     */
    public final function setFormatter(ehough_epilog_api_IFormatter $formatter)
    {
        $this->formatter = $formatter;
    }

    /**
     * {@inheritdoc}
     */
    public final function getFormatter()
    {
        if (!$this->formatter) {
            $this->formatter = $this->getDefaultFormatter();
        }

        return $this->formatter;
    }


    /**
     * Sets minimum logging level at which this handler will be triggered.
     *
     * @param integer $level
     */
    public final function setLevel($level)
    {
        $this->level = $level;
    }

    /**
     * Gets minimum logging level at which this handler will be triggered.
     *
     * @return integer
     */
    public final function getLevel()
    {
        return $this->level;
    }

    /**
     * Sets the bubbling behavior.
     *
     * @param Boolean $bubble True means that bubbling is not permitted.
     *                        False means that this handler allows bubbling.
     */
    public final function setBubble($bubble)
    {
        $this->bubble = $bubble;
    }

    /**
     * Gets the bubbling behavior.
     *
     * @return Boolean True means that bubbling is not permitted.
     *                 False means that this handler allows bubbling.
     */
    public final function getBubble()
    {
        return $this->bubble;
    }

    public final function __destruct()
    {
        try {

            $this->close();

        } catch (Exception $e) {

            // do nothing
        }
    }

    /**
     * Closes the handler.
     *
     * This will be called automatically when the object is destroyed
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

    protected final function getProcessors()
    {
        return $this->processors;
    }
}
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
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

class ehough_epilog_impl_StandardLogger implements ehough_epilog_api_ILogger
{
    private static $_levels = array(

                               100 => 'DEBUG',
                               200 => 'INFO',
                               300 => 'WARNING',
                               400 => 'ERROR',
                               500 => 'CRITICAL',
                              );

    private $_name;

    private $_handlers = array();

    private $_processors = array();

    /**
     * Constructor.
     *
     * @param string $name The logging channel.
     */
    public function __construct($name)
    {
        date_default_timezone_set(date_default_timezone_get());

        $this->_name = $name;
    }

    /**
     * Get the name of this logger instance.
     *
     * @return string The name of this logger instance.
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Determines if level debug and above is enabled.
     *
     * @return boolean True if debug and above is enabled, false otherwise.
     */
    public function isDebugEnabled()
    {
        return $this->_isHandling(ehough_epilog_api_ILogger::DEBUG);
    }

    /**
     * Determines if level info and above is enabled.
     *
     * @return boolean True if info and above is enabled, false otherwise.
     */
    public function isInfoEnabled()
    {
        return $this->_isHandling(ehough_epilog_api_ILogger::INFO);
    }

    /**
     * Determines if level warn and above is enabled.
     *
     * @return boolean True if warn and above is enabled, false otherwise.
     */
    public function isWarnEnabled()
    {
        return $this->_isHandling(ehough_epilog_api_ILogger::WARNING);
    }

    /**
     * Determines if level error and above is enabled.
     *
     * @return boolean True if error and above is enabled, false otherwise.
     */
    public function isErrorEnabled()
    {
        return $this->_isHandling(ehough_epilog_api_ILogger::ERROR);
    }

    /**
     * Determines if level critical and above is enabled.
     *
     * @return boolean True if critical and above is enabled, false otherwise.
     */
    public function isCriticalEnabled()
    {
        return $this->_isHandling(ehough_epilog_api_ILogger::CRITICAL);
    }

    /**
     * Adds a log record at the DEBUG level.
     *
     * This method allows for compatibility with common interfaces.
     *
     * @param string $message The log message.
     * @param array  $context The log context.
     *
     * @return boolean Whether the record has been processed
     */
    public function debug($message, array $context = array())
    {
        return $this->_addRecord(ehough_epilog_api_ILogger::DEBUG, $message, $context);
    }

    /**
     * Adds a log record at the INFO level.
     *
     * This method allows for compatibility with common interfaces.
     *
     * @param string $message The log message.
     * @param array  $context The log context.
     *
     * @return boolean Whether the record has been processed
     */
    public function info($message, array $context = array())
    {
        return $this->_addRecord(ehough_epilog_api_ILogger::INFO, $message, $context);
    }

    /**
     * Adds a log record at the WARNING level.
     *
     * This method allows for compatibility with common interfaces.
     *
     * @param string $message The log message.
     * @param array  $context The log context.
     *
     * @return boolean Whether the record has been processed
     */
    public function warn($message, array $context = array())
    {
        return $this->_addRecord(ehough_epilog_api_ILogger::WARNING, $message, $context);
    }

    /**
     * Adds a log record at the ERROR level.
     *
     * This method allows for compatibility with common interfaces.
     *
     * @param string $message The log message.
     * @param array  $context The log context.
     *
     * @return boolean Whether the record has been processed.
     */
    public function error($message, array $context = array())
    {
        return $this->_addRecord(ehough_epilog_api_ILogger::ERROR, $message, $context);
    }

    /**
     * Adds a log record at the CRITICAL level.
     *
     * This method allows for compatibility with common interfaces.
     *
     * @param string $message The log message.
     * @param array  $context The log context.
     *
     * @return boolean Whether the record has been processed
     */
    public function critical($message, array $context = array())
    {
        return $this->_addRecord(ehough_epilog_api_ILogger::CRITICAL, $message, $context);
    }

    /**
     * Pushes a handler on to the stack.
     *
     * @param ehough_epilog_api_IHandler $handler The handler to push.
     *
     * @return void
     */
    public function pushHandler(ehough_epilog_api_IHandler $handler)
    {
        array_unshift($this->_handlers, $handler);
    }

    /**
     * Pops a handler from the stack.
     *
     * @return ehough_epilog_api_IHandler The top handler on the stack.
     *
     * @throws LogicException If the handler stack is empty.
     */
    public function popHandler()
    {
        if (count($this->_handlers) === 0) {

            throw new LogicException('You tried to pop from an empty handler stack.');
        }

        return array_shift($this->_handlers);
    }

    /**
     * Adds a processor on to the stack.
     *
     * @param ehough_epilog_api_IProcessor $callback The processor to push.
     *
     * @return void
     */
    public function pushProcessor(ehough_epilog_api_IProcessor $callback)
    {
        array_unshift($this->_processors, $callback);
    }

    /**
     * Removes the processor on top of the stack and returns it.
     *
     * @return ehough_epilog_api_IProcessor
     *
     * @throws LogicException If the stack of processors is currently empty.
     */
    public function popProcessor()
    {
        if (count($this->_processors) === 0) {

            throw new LogicException('You tried to pop from an empty processor stack.');
        }

        return array_shift($this->_processors);
    }

    /**
     * Checks whether the Logger has a handler that listens on the given level.
     *
     * @param integer $level The integral logging level to check.
     *
     * @return boolean True if this logger is handling this level, false otherwise.
     */
    private function _isHandling($level)
    {
        $record = array(

                   'message'    => '',
                   'context'    => array(),
                   'level'      => $level,
                   'level_name' => $this->_getLevelName($level),
                   'channel'    => $this->_name,
                   'time'       => new ehough_epilog_impl_TimeStamp(),
                   'extra'      => array(),
                  );

        /* @noinspection PhpUnusedLocalVariableInspection */
        foreach ($this->_handlers as $key => $handler) {

            /** @noinspection PhpUndefinedMethodInspection */
            if ($handler->isHandling($record)) {

                return true;
            }
        }

        return false;
    }

    /**
     * Adds a log record.
     *
     * @param integer $level   The logging level.
     * @param string  $message The log message.
     * @param array   $context The log context.
     *
     * @return Boolean Whether the record has been processed.
     */
    private function _addRecord($level, $message, array $context = array())
    {
        if (count($this->_handlers) === 0) {

            $handler = new ehough_epilog_impl_handler_StreamHandler('php://stderr', ehough_epilog_api_ILogger::DEBUG);

            $this->pushHandler($handler);
        }

        $record = array(

                   'message'    => (string) $message,
                   'context'    => $context,
                   'level'      => $level,
                   'level_name' => $this->_getLevelName($level),
                   'channel'    => $this->_name,
                   'time'       => new ehough_epilog_impl_TimeStamp(),
                   'extra'      => array(),
                  );

        // check if any message will handle this message
        $handlerKey = null;

        foreach ($this->_handlers as $key => $handler) {

            /** @noinspection PhpUndefinedMethodInspection */
            if ($handler->isHandling($record)) {

                $handlerKey = $key;

                break;
            }
        }

        // none found
        if ($handlerKey === null) {

            return false;
        }

        // found at least one, process message and dispatch it
        foreach ($this->_processors as $processor) {

            /** @noinspection PhpUndefinedMethodInspection */
            $record = $processor->process($record);
        }

        while (isset($this->_handlers[$handlerKey]) && $this->_handlers[$handlerKey]->handle($record) === false) {

            $handlerKey++;
        }

        return true;
    }

    /**
     * Gets the name of the logging level.
     *
     * @param integer $level The integral logging level.
     *
     * @return string The string representation of the logging level, or "undefined".
     */
    private function _getLevelName($level)
    {
        return isset(self::$_levels[$level]) ? self::$_levels[$level] : 'undefined';
    }
}
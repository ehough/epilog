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

/**
 * Log channel
 *
 * It contains a stack of Handlers and a stack of Processors,
 * and uses them to store records that are added to it.
 *
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
interface ehough_epilog_api_ILogger
{
    /**
     * Detailed debug information
     */
    const DEBUG = 100;

    /**
     * Interesting events
     *
     * Examples: User logs in, SQL logs.
     */
    const INFO = 200;

    /**
     * Exceptional occurrences that are not errors
     *
     * Examples: Use of deprecated APIs, poor use of an API,
     * undesirable things that are not necessarily wrong.
     */
    const WARNING = 300;

    /**
     * Runtime errors
     */
    const ERROR = 400;

    /**
     * Critical conditions
     *
     * Example: Application component unavailable, unexpected exception.
     */
    const CRITICAL = 500;

    /**
     * Get the name of this logger instance.
     *
     * @return string The name of this logger instance.
     */
    function getName();

    /**
     * Determines if level debug and above is enabled.
     *
     * @return boolean True if debug and above is enabled, false otherwise.
     */
    function isDebugEnabled();

    /**
     * Determines if level info and above is enabled.
     *
     * @return boolean True if info and above is enabled, false otherwise.
     */
    function isInfoEnabled();

    /**
     * Determines if level warn and above is enabled.
     *
     * @return boolean True if warn and above is enabled, false otherwise.
     */
    function isWarnEnabled();

    /**
     * Determines if level error and above is enabled.
     *
     * @return boolean True if error and above is enabled, false otherwise.
     */
    function isErrorEnabled();

    /**
     * Determines if level critical and above is enabled.
     *
     * @return boolean True if critical and above is enabled, false otherwise.
     */
    function isCriticalEnabled();

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
    function debug($message, array $context = array());

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
    function info($message, array $context = array());

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
    function warn($message, array $context = array());

    /**
     * Adds a log record at the ERROR level.
     *
     * This method allows for compatibility with common interfaces.
     *
     * @param string $message The log message.
     * @param array  $context The log context.
     *
     * @return boolean Whether the record has been processed
     */
    function error($message, array $context = array());

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
    function critical($message, array $context = array());
}
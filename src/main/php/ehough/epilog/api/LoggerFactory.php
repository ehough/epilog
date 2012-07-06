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
 * Convenience class to build loggers.
 */
final class ehough_epilog_api_LoggerFactory
{
    private static $_nameToLoggerMap = array();

    private static $_processorStack = array();

    private static $_handlerStack = array();

    /**
     * Builds or fetches the logger for the given name.
     *
     * @param string $name The name of the logger to build or fetch.
     *
     * @return ehough_epilog_api_ILogger The logger for the given name.
     */
    public static function getLogger($name)
    {
        if (!isset(self::$_nameToLoggerMap[$name])) {

            self::$_nameToLoggerMap[$name] = self::_buildLogger($name);
        }

        return self::$_nameToLoggerMap[$name];
    }

    /**
     * Set the stack of handlers for loggers built from this factory.
     *
     * @param array $stack The stack of handlers for loggers built from this factory.
     *
     * @return void
     */
    public static function setHandlerStack(array $stack)
    {
        self::$_handlerStack = $stack;
    }

    /**
     * Set the stack of processors for loggers built from this factory.
     *
     * @param array $stack The stack of processors for loggers built from this factory.
     *
     * @return void
     */
    public static function setProcessorStack(array $stack)
    {
        self::$_processorStack = $stack;
    }

    /**
     * Build a logger with the given name.
     *
     * @param string $name The name of the logger to build.
     *
     * @return ehough_epilog_api_ILogger The logger for the given name.
     */
    private static function _buildLogger($name)
    {
        $toReturn = new ehough_epilog_impl_StandardLogger($name);

        /**
         * No handlers?
         */
        if (count(self::$_handlerStack) === 0) {

            $handler = new ehough_epilog_impl_handler_NullHandler();

            $toReturn->pushHandler($handler);

            return $toReturn;
        }

        foreach (self::$_processorStack as $processor) {

            $toReturn->pushProcessor($processor);
        }

        foreach (self::$_handlerStack as $handler) {

            $toReturn->pushHandler($handler);
        }

        return $toReturn;
    }
}
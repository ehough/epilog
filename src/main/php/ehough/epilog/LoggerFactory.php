<?php
/*
 * This file is part of the epilog package.
 *
 * (c) Eric Hough <ehough.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Convenience class to build loggers.
 */
final class ehough_epilog_LoggerFactory
{
    private static $_nameToLoggerMap = array();

    private static $_processorStack = array();

    private static $_handlerStack = array();

    /**
     * Builds or fetches the logger for the given name.
     *
     * @param string $name The name of the logger to build or fetch.
     *
     * @return ehough_epilog_psr_LoggerInterface The logger for the given name.
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
     * @return ehough_epilog_psr_LoggerInterface The logger for the given name.
     */
    private static function _buildLogger($name)
    {
        $toReturn = new ehough_epilog_Logger($name);

        /**
         * No handlers?
         */
        if (count(self::$_handlerStack) === 0) {

            $handler = new ehough_epilog_handler_NullHandler();

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
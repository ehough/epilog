<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Monolog error handler
 *
 * A facility to enable logging of runtime errors, exceptions and fatal errors.
 *
 * Quick setup: <code>ErrorHandler::register($logger);</code>
 *
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
class ehough_epilog_ErrorHandler
{
    private $logger;

    private $previousExceptionHandler;
    private $uncaughtExceptionLevel;

    private $previousErrorHandler;
    private $errorLevelMap;

    private $fatalLevel;
    private $reservedMemory;
    private static $fatalErrors = array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR);

    public function __construct(ehough_epilog_psr_LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Registers a new ehough_epilog_ErrorHandler for a given ehough_epilog_Logger
     *
     * By default it will handle errors, exceptions and fatal errors
     *
     * @param  ehough_epilog_psr_LoggerInterface $logger
     * @param  array|false     $errorLevelMap  an array of E_* constant to ehough_epilog_psr_LogLevel::* constant mapping, or false to disable error handling
     * @param  int|false       $exceptionLevel a ehough_epilog_psr_LogLevel::* constant, or false to disable exception handling
     * @param  int|false       $fatalLevel     a ehough_epilog_psr_LogLevel::* constant, or false to disable fatal error handling
     * @return ehough_epilog_ErrorHandler
     */
    public static function register(ehough_epilog_psr_LoggerInterface $logger, $errorLevelMap = array(), $exceptionLevel = null, $fatalLevel = null)
    {
        $handler = new self($logger);
        if ($errorLevelMap !== false) {
            $handler->registerErrorHandler($errorLevelMap);
        }
        if ($exceptionLevel !== false) {
            $handler->registerExceptionHandler($exceptionLevel);
        }
        if ($fatalLevel !== false) {
            $handler->registerFatalHandler($fatalLevel);
        }

        return $handler;
    }

    public function registerExceptionHandler($level = null, $callPrevious = true)
    {
        $prev = set_exception_handler(array($this, 'handleException'));
        $this->uncaughtExceptionLevel = $level;
        if ($callPrevious && $prev) {
            $this->previousExceptionHandler = $prev;
        }
    }

    public function registerErrorHandler(array $levelMap = array(), $callPrevious = true, $errorTypes = -1)
    {
        $prev = set_error_handler(array($this, 'handleError'), $errorTypes);
        $this->errorLevelMap = $this->defaultErrorLevelMap();
        // merging the map into the defaults by hand because array_merge
        // trips up on numeric keys
        foreach ($levelMap as $key => $val) {
            $this->errorLevelMap[$key] = $val;
        }
        if ($callPrevious) {
            $this->previousErrorHandler = $prev ? $prev : true;
        }
    }

    public function registerFatalHandler($level = null, $reservedMemorySize = 20)
    {
        register_shutdown_function(array($this, 'handleFatalError'));

        $this->reservedMemory = str_repeat(' ', 1024 * $reservedMemorySize);
        $this->fatalLevel = $level;
    }

    protected function defaultErrorLevelMap()
    {
        return array(
            E_ERROR             => ehough_epilog_psr_LogLevel::CRITICAL,
            E_WARNING           => ehough_epilog_psr_LogLevel::WARNING,
            E_PARSE             => ehough_epilog_psr_LogLevel::ALERT,
            E_NOTICE            => ehough_epilog_psr_LogLevel::NOTICE,
            E_CORE_ERROR        => ehough_epilog_psr_LogLevel::CRITICAL,
            E_CORE_WARNING      => ehough_epilog_psr_LogLevel::WARNING,
            E_COMPILE_ERROR     => ehough_epilog_psr_LogLevel::ALERT,
            E_COMPILE_WARNING   => ehough_epilog_psr_LogLevel::WARNING,
            E_USER_ERROR        => ehough_epilog_psr_LogLevel::ERROR,
            E_USER_WARNING      => ehough_epilog_psr_LogLevel::WARNING,
            E_USER_NOTICE       => ehough_epilog_psr_LogLevel::NOTICE,
            E_STRICT            => ehough_epilog_psr_LogLevel::NOTICE,
            E_RECOVERABLE_ERROR => ehough_epilog_psr_LogLevel::ERROR,
            E_DEPRECATED        => ehough_epilog_psr_LogLevel::NOTICE,
            E_USER_DEPRECATED   => ehough_epilog_psr_LogLevel::NOTICE,
        );
    }

    /**
     * @private
     */
    public function handleException(Exception $e)
    {
        $this->logger->log(
            $this->uncaughtExceptionLevel === null ? ehough_epilog_psr_LogLevel::ERROR : $this->uncaughtExceptionLevel,
            'Uncaught exception',
            array('exception' => $e)
        );

        if ($this->previousExceptionHandler) {
            call_user_func($this->previousExceptionHandler, $e);
        }
    }

    /**
     * @private
     */
    public function handleError($code, $message, $file = '', $line = 0, $context = array())
    {
        if (!(error_reporting() & $code)) {
            return;
        }

        $level = isset($this->errorLevelMap[$code]) ? $this->errorLevelMap[$code] : ehough_epilog_psr_LogLevel::CRITICAL;
        $this->logger->log($level, self::codeToString($code).': '.$message, array('file' => $file, 'line' => $line));

        if ($this->previousErrorHandler === true) {
            return false;
        } elseif ($this->previousErrorHandler) {
            return call_user_func($this->previousErrorHandler, $code, $message, $file, $line, $context);
        }
    }

    /**
     * @private
     */
    public function handleFatalError()
    {
        $this->reservedMemory = null;

        $lastError = error_get_last();
        if ($lastError && in_array($lastError['type'], self::$fatalErrors)) {
            $this->logger->log(
                $this->fatalLevel === null ? ehough_epilog_psr_LogLevel::ALERT : $this->fatalLevel,
                'Fatal Error ('.self::codeToString($lastError['type']).'): '.$lastError['message'],
                array('file' => $lastError['file'], 'line' => $lastError['line'])
            );
        }
    }

    private static function codeToString($code)
    {
        switch ($code) {
            case E_ERROR:
                return 'E_ERROR';
            case E_WARNING:
                return 'E_WARNING';
            case E_PARSE:
                return 'E_PARSE';
            case E_NOTICE:
                return 'E_NOTICE';
            case E_CORE_ERROR:
                return 'E_CORE_ERROR';
            case E_CORE_WARNING:
                return 'E_CORE_WARNING';
            case E_COMPILE_ERROR:
                return 'E_COMPILE_ERROR';
            case E_COMPILE_WARNING:
                return 'E_COMPILE_WARNING';
            case E_USER_ERROR:
                return 'E_USER_ERROR';
            case E_USER_WARNING:
                return 'E_USER_WARNING';
            case E_USER_NOTICE:
                return 'E_USER_NOTICE';
            case E_STRICT:
                return 'E_STRICT';
            case E_RECOVERABLE_ERROR:
                return 'E_RECOVERABLE_ERROR';
            case E_DEPRECATED:
                return 'E_DEPRECATED';
            case E_USER_DEPRECATED:
                return 'E_USER_DEPRECATED';
        }

        return 'Unknown PHP error';
    }
}

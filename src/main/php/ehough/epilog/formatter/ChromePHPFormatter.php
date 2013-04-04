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
 * Formats a log message according to the ChromePHP array format
 *
 * @author Christophe Coevoet <stof@notk.org>
 */
class ehough_epilog_formatter_ChromePHPFormatter implements ehough_epilog_formatter_FormatterInterface
{
    /**
     * Translates Monolog log levels to Wildfire levels.
     */
    private $logLevels = array(
        ehough_epilog_Logger::DEBUG     => 'log',
        ehough_epilog_Logger::INFO      => 'info',
        ehough_epilog_Logger::NOTICE    => 'info',
        ehough_epilog_Logger::WARNING   => 'warn',
        ehough_epilog_Logger::ERROR     => 'error',
        ehough_epilog_Logger::CRITICAL  => 'error',
        ehough_epilog_Logger::ALERT     => 'error',
        ehough_epilog_Logger::EMERGENCY => 'error',
    );

    /**
     * {@inheritdoc}
     */
    public function format(array $record)
    {
        // Retrieve the line and file if set and remove them from the formatted extra
        $backtrace = 'unknown';
        if (isset($record['extra']['file']) && isset($record['extra']['line'])) {
            $backtrace = $record['extra']['file'].' : '.$record['extra']['line'];
            unset($record['extra']['file']);
            unset($record['extra']['line']);
        }

        $message = array('message' => $record['message']);
        if ($record['context']) {
            $message['context'] = $record['context'];
        }
        if ($record['extra']) {
            $message['extra'] = $record['extra'];
        }
        if (count($message) === 1) {
            $message = reset($message);
        }

        return array(
            $record['channel'],
            $message,
            $backtrace,
            $this->logLevels[$record['level']],
        );
    }

    public function formatBatch(array $records)
    {
        $formatted = array();

        foreach ($records as $record) {
            $formatted[] = $this->format($record);
        }

        return $formatted;
    }
}

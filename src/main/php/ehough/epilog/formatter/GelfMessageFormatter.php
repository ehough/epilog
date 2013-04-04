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
 * Serializes a log message to GELF
 * @see http://www.graylog2.org/about/gelf
 *
 * @author Matt Lehner <mlehner@gmail.com>
 */
class ehough_epilog_formatter_GelfMessageFormatter extends ehough_epilog_formatter_NormalizerFormatter
{
    /**
     * @var string the name of the system for the Gelf log message
     */
    protected $systemName;

    /**
     * @var string a prefix for 'extra' fields from the Monolog record (optional)
     */
    protected $extraPrefix;

    /**
     * @var string a prefix for 'context' fields from the Monolog record (optional)
     */
    protected $contextPrefix;

    /**
     * Translates Monolog log levels to Graylog2 log priorities.
     */
    private $logLevels = array(
        ehough_epilog_Logger::DEBUG     => 7,
        ehough_epilog_Logger::INFO      => 6,
        ehough_epilog_Logger::NOTICE    => 5,
        ehough_epilog_Logger::WARNING   => 4,
        ehough_epilog_Logger::ERROR     => 3,
        ehough_epilog_Logger::CRITICAL  => 2,
        ehough_epilog_Logger::ALERT     => 1,
        ehough_epilog_Logger::EMERGENCY => 0,
    );

    public function __construct($systemName = null, $extraPrefix = null, $contextPrefix = 'ctxt_')
    {
        parent::__construct('U.u');

        $this->systemName = $systemName ? $systemName : gethostname();

        $this->extraPrefix = $extraPrefix;
        $this->contextPrefix = $contextPrefix;
    }

    /**
     * {@inheritdoc}
     */
    public function format(array $record)
    {
        $record = parent::format($record);
        $message = new \Gelf\Message();
        $message
            ->setTimestamp($record['datetime'])
            ->setShortMessage((string) $record['message'])
            ->setFacility($record['channel'])
            ->setHost($this->systemName)
            ->setLine(isset($record['extra']['line']) ? $record['extra']['line'] : null)
            ->setFile(isset($record['extra']['file']) ? $record['extra']['file'] : null)
            ->setLevel($this->logLevels[$record['level']]);

        // Do not duplicate these values in the additional fields
        unset($record['extra']['line']);
        unset($record['extra']['file']);

        foreach ($record['extra'] as $key => $val) {
            $message->setAdditional($this->extraPrefix . $key, is_scalar($val) ? $val : $this->toJson($val));
        }

        foreach ($record['context'] as $key => $val) {
            $message->setAdditional($this->contextPrefix . $key, is_scalar($val) ? $val : $this->toJson($val));
        }

        return $message;
    }
}

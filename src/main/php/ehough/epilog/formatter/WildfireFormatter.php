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
 * Serializes a log message according to Wildfire's header requirements
 *
 * @author Eric Clemmons (@ericclemmons) <eric@uxdriven.com>
 * @author Christophe Coevoet <stof@notk.org>
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 */
class ehough_epilog_formatter_WildfireFormatter extends ehough_epilog_formatter_NormalizerFormatter
{
    /**
     * Translates Monolog log levels to Wildfire levels.
     */
    private $logLevels = array(
        ehough_epilog_Logger::DEBUG     => 'LOG',
        ehough_epilog_Logger::INFO      => 'INFO',
        ehough_epilog_Logger::NOTICE    => 'INFO',
        ehough_epilog_Logger::WARNING   => 'WARN',
        ehough_epilog_Logger::ERROR     => 'ERROR',
        ehough_epilog_Logger::CRITICAL  => 'ERROR',
        ehough_epilog_Logger::ALERT     => 'ERROR',
        ehough_epilog_Logger::EMERGENCY => 'ERROR',
    );

    /**
     * {@inheritdoc}
     */
    public function format(array $record)
    {
        // Retrieve the line and file if set and remove them from the formatted extra
        $file = $line = '';
        if (isset($record['extra']['file'])) {
            $file = $record['extra']['file'];
            unset($record['extra']['file']);
        }
        if (isset($record['extra']['line'])) {
            $line = $record['extra']['line'];
            unset($record['extra']['line']);
        }

        $record = $this->normalize($record);
        $message = array('message' => $record['message']);
        $handleError = false;
        if ($record['context']) {
            $message['context'] = $record['context'];
            $handleError = true;
        }
        if ($record['extra']) {
            $message['extra'] = $record['extra'];
            $handleError = true;
        }
        if (count($message) === 1) {
            $message = reset($message);
        }

        // Create JSON object describing the appearance of the message in the console
        $json = $this->toJson(array(
            array(
                'Type'  => $this->logLevels[$record['level']],
                'File'  => $file,
                'Line'  => $line,
                'Label' => $record['channel'],
            ),
            $message,
        ), $handleError);

        // The message itself is a serialization of the above JSON object + it's length
        return sprintf(
            '%s|%s|',
            strlen($json),
            $json
        );
    }

    public function formatBatch(array $records)
    {
        throw new BadMethodCallException('Batch formatting does not make sense for the WildfireFormatter');
    }

    protected function normalize($data)
    {
        if (is_object($data) && !$data instanceof DateTime) {
            return $data;
        }

        return parent::normalize($data);
    }
}

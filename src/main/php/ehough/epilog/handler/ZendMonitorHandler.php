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
 * Handler sending logs to Zend Monitor
 *
 * @author  Christian Bergau <cbergau86@gmail.com>
 */
class ehough_epilog_handler_ZendMonitorHandler extends ehough_epilog_handler_AbstractProcessingHandler
{
    /**
     * Monolog level / ZendMonitor Custom Event priority map
     *
     * @var array
     */
    protected $levelMap = array(
        ehough_epilog_Logger::DEBUG     => 1,
        ehough_epilog_Logger::INFO      => 2,
        ehough_epilog_Logger::NOTICE    => 3,
        ehough_epilog_Logger::WARNING   => 4,
        ehough_epilog_Logger::ERROR     => 5,
        ehough_epilog_Logger::CRITICAL  => 6,
        ehough_epilog_Logger::ALERT     => 7,
        ehough_epilog_Logger::EMERGENCY => 0,
    );

    /**
     * Construct
     *
     * @param  int                       $level
     * @param  bool                      $bubble
     * @throws ehough_epilog_handler_MissingExtensionException
     */
    public function __construct($level = ehough_epilog_Logger::DEBUG, $bubble = true)
    {
        if (!function_exists('zend_monitor_custom_event')) {
            throw new ehough_epilog_handler_MissingExtensionException('You must have Zend Server installed in order to use this handler');
        }
        parent::__construct($level, $bubble);
    }

    /**
     * {@inheritdoc}
     */
    protected function write(array $record)
    {
        $this->writeZendMonitorCustomEvent(
            $this->levelMap[$record['level']],
            $record['message'],
            $record['formatted']
        );
    }

    /**
     * Write a record to Zend Monitor
     *
     * @param int    $level
     * @param string $message
     * @param array  $formatted
     */
    protected function writeZendMonitorCustomEvent($level, $message, $formatted)
    {
        zend_monitor_custom_event($level, $message, $formatted);
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultFormatter()
    {
        return new ehough_epilog_formatter_NormalizerFormatter();
    }

    /**
     * Get the level map
     *
     * @return array
     */
    public function getLevelMap()
    {
        return $this->levelMap;
    }
}

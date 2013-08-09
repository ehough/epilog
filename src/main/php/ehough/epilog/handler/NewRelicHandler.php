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
 * Class to record a log on a NewRelic application
 *
 * @see https://newrelic.com/docs/php/new-relic-for-php
 */
class ehough_epilog_handler_NewRelicHandler extends ehough_epilog_handler_AbstractProcessingHandler
{
    /**
     * {@inheritDoc}
     */
    public function __construct($level = ehough_epilog_Logger::ERROR, $bubble = true)
    {
        parent::__construct($level, $bubble);
    }

    /**
     * {@inheritDoc}
     */
    protected function write(array $record)
    {
        if (!$this->isNewRelicEnabled()) {
            throw new ehough_epilog_handler_MissingExtensionException('The newrelic PHP extension is required to use the NewRelicHandler');
        }

        if (isset($record['context']['exception']) && $record['context']['exception'] instanceof Exception) {
            newrelic_notice_error($record['message'], $record['context']['exception']);
            unset($record['context']['exception']);
        } else {
            newrelic_notice_error($record['message']);
        }

        foreach ($record['context'] as $key => $parameter) {
            newrelic_add_custom_parameter($key, $parameter);
        }
    }

    /**
     * Checks whether the NewRelic extension is enabled in the system.
     *
     * @return bool
     */
    protected function isNewRelicEnabled()
    {
        return extension_loaded('newrelic');
    }
}
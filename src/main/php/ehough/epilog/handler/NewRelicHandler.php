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
     * Name of the New Relic application that will receive logs from this handler.
     *
     * @var string
     */
    protected $appName;

    /**
     * {@inheritDoc}
     *
     * @param string $appName
     */
    public function __construct($level = ehough_epilog_Logger::ERROR, $bubble = true, $appName = null)
    {
        parent::__construct($level, $bubble);

        $this->appName = $appName;
    }

    /**
     * {@inheritDoc}
     */
    protected function write(array $record)
    {
        if (!$this->isNewRelicEnabled()) {
            throw new ehough_epilog_handler_MissingExtensionException('The newrelic PHP extension is required to use the NewRelicHandler');
        }

        if ($appName = $this->getAppName($record['context'])) {
            $this->setNewRelicAppName($appName);
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

        foreach ($record['extra'] as $key => $parameter) {
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

    /**
     * Returns the appname where this log should be sent. Each log can override the default appname, set in this
     * handler's constructor, by providing the appname in its context.
     *
     * @param  array       $context
     * @return null|string
     */
    protected function getAppName(array $context)
    {
        if (isset($context['appname'])) {
            return $context['appname'];
        }

        return $this->appName;
    }

    /**
     * Sets the NewRelic application that should receive this log.
     *
     * @param string $appName
     */
    protected function setNewRelicAppName($appName)
    {
        newrelic_set_appname($appName);
    }
}

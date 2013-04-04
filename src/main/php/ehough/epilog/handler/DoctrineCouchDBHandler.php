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
 * CouchDB handler for Doctrine CouchDB ODM
 *
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class ehough_epilog_handler_DoctrineCouchDBHandler extends ehough_epilog_handler_AbstractProcessingHandler
{
    private $client;

    public function __construct(Doctrine\CouchDB\CouchDBClient $client, $level = ehough_epilog_Logger::DEBUG, $bubble = true)
    {
        $this->client = $client;
        parent::__construct($level, $bubble);
    }

    /**
     * {@inheritDoc}
     */
    protected function write(array $record)
    {
        $this->client->postDocument($record['formatted']);
    }

    protected function getDefaultFormatter()
    {
        return new ehough_epilog_formatter_NormalizerFormatter;
    }
}

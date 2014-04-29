<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//use Elastica\Client;
//use Elastica\Exception\ExceptionInterface;

/**
 * Elastic Search handler
 *
 * Usage example:
 *
 *    $client = new \Elastica\Client();
 *    $options = array(
 *        'index' => 'elastic_index_name',
 *        'type' => 'elastic_doc_type',
 *    );
 *    $handler = new ElasticSearchHandler($client, $options);
 *    $log = new Logger('application');
 *    $log->pushHandler($handler);
 *
 * @author Jelle Vink <jelle.vink@gmail.com>
 */
class ehough_epilog_handler_ElasticSearchHandler extends ehough_epilog_handler_AbstractProcessingHandler
{
    /**
     * @var Elastica\Client
     */
    protected $client;

    /**
     * @var array Handler config options
     */
    protected $options = array();

    /**
     * @param Elastica\Client  $client  Elastica Client object
     * @param array            $options Handler configuration
     * @param integer          $level   The minimum logging level at which this handler will be triggered
     * @param Boolean          $bubble  Whether the messages that are handled can bubble up the stack or not
     */
    public function __construct(Elastica\Client $client, array $options = array(), $level = ehough_epilog_Logger::DEBUG, $bubble = true)
    {
        parent::__construct($level, $bubble);
        $this->client = $client;
        $this->options = array_merge(
            array(
                'index'          => 'monolog',      // Elastic index name
                'type'           => 'record',       // Elastic document type
                'ignore_error'   => false,          // Suppress Elastica exceptions
            ),
            $options
        );
    }

    /**
     * {@inheritDoc}
     */
    protected function write(array $record)
    {
        $this->bulkSend(array($record['formatted']));
    }

    /**
     * {@inheritdoc}
     */
    public function setFormatter(ehough_epilog_formatter_FormatterInterface $formatter)
    {
        if ($formatter instanceof ehough_epilog_formatter_ElasticaFormatter) {
            return parent::setFormatter($formatter);
        }
        throw new InvalidArgumentException('ElasticSearchHandler is only compatible with ElasticaFormatter');
    }

    /**
     * Getter options
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * {@inheritDoc}
     */
    protected function getDefaultFormatter()
    {
        return new ehough_epilog_formatter_ElasticaFormatter($this->options['index'], $this->options['type']);
    }

    /**
     * {@inheritdoc}
     */
    public function handleBatch(array $records)
    {
        $documents = $this->getFormatter()->formatBatch($records);
        $this->bulkSend($documents);
    }

    /**
     * Use Elasticsearch bulk API to send list of documents
     * @param  array             $documents
     * @throws \RuntimeException
     */
    protected function bulkSend(array $documents)
    {
        try {
            $this->client->addDocuments($documents);
        } catch (Elastica\Exception\ExceptionInterface $e) {
            if (!$this->options['ignore_error']) {
                throw new RuntimeException("Error sending messages to Elasticsearch", 0, $e);
            }
        }
    }
}

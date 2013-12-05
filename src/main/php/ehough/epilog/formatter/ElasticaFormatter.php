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
 * Format a log message into an Elastica Document
 *
 * @author Jelle Vink <jelle.vink@gmail.com>
 */
class ehough_epilog_formatter_ElasticaFormatter extends ehough_epilog_formatter_NormalizerFormatter
{
    /**
     * @var string Elastic search index name
     */
    protected $index;

    /**
     * @var string Elastic search document type
     */
    protected $type;

    /**
     * @param string $index Elastic Search index name
     * @param string $type  Elastic Search document type
     */
    public function __construct($index, $type)
    {
        parent::__construct(DateTime::ISO8601);
        $this->index = $index;
        $this->type = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function format(array $record)
    {
        $record = parent::format($record);
        return $this->getDocument($record);
    }

    /**
     * Getter index
     * @return string
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * Getter type
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Convert a log message into an Elastica Document
     *
     * @param array  $record Log message
     * @return \Elastica\Document
     */
    protected function getDocument($record)
    {
        $document = new Elastica\Document();
        $document->setData($record);
        $document->setType($this->type);
        $document->setIndex($this->index);
        return $document;
    }
}

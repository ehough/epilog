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
 * Encodes whatever record data is passed to it as json
 *
 * This can be useful to log to databases or remote APIs
 *
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
class ehough_epilog_formatter_JsonFormatter implements ehough_epilog_formatter_FormatterInterface
{
    /**
     * {@inheritdoc}
     */
    public function format(array $record)
    {
        return json_encode($record);
    }

    /**
     * {@inheritdoc}
     */
    public function formatBatch(array $records)
    {
        return json_encode($records);
    }
}

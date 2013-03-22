<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//namespace Monolog\Processor;

//use Monolog\TestCase;

class ehough_epilog_processor_UidProcessorTest extends ehough_epilog_TestCase
{
    /**
     * @covers Monolog\Processor\UidProcessor::__invoke
     */
    public function testProcessor()
    {
        $processor = new ehough_epilog_processor_UidProcessor();
        $record = $processor($this->getRecord());
        $this->assertArrayHasKey('uid', $record['extra']);
    }
}
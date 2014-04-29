<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


class GitProcessorTest extends ehough_epilog_TestCase
{
    /**
     * @covers ehough_epilog_processor_GitProcessor::__invoke
     */
    public function testProcessor()
    {
        $processor = new ehough_epilog_processor_GitProcessor();
        $record = $processor->__invoke($this->getRecord());

        $this->assertArrayHasKey('git', $record['extra']);
        $this->assertTrue(!is_array($record['extra']['git']['branch']));
    }
}

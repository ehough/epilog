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

class ehough_epilog_processor_ProcessIdProcessorTest extends ehough_epilog_TestCase
{
    /**
     * @covers Monolog\Processor\ProcessIdProcessor::__invoke
     */
    public function testProcessor()
    {
        $processor = new ehough_epilog_processor_ProcessIdProcessor();
        $record = call_user_func(array($processor, '__invoke'), $this->getRecord());
        $this->assertArrayHasKey('process_id', $record['extra']);
        $this->assertInternalType('int', $record['extra']['process_id']);
        $this->assertGreaterThan(0, $record['extra']['process_id']);
        $this->assertEquals(getmypid(), $record['extra']['process_id']);
    }
}

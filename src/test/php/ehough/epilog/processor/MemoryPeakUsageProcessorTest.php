<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class ehough_epilog_processor_MemoryPeakUsageProcessorTest extends ehough_epilog_TestCase
{
    /**
     * @covers ehough_epilog_processor_MemoryPeakUsageProcessor::__invoke
     * @covers ehough_epilog_processor_MemoryProcessor::formatBytes
     */
    public function testProcessor()
    {
        $processor = new ehough_epilog_processor_MemoryPeakUsageProcessor();
        $record = call_user_func(array($processor, '__invoke'), $this->getRecord());
        $this->assertArrayHasKey('memory_peak_usage', $record['extra']);
        $this->assertRegExp('#[0-9.]+ (M|K)?B$#', $record['extra']['memory_peak_usage']);
    }

    /**
     * @covers ehough_epilog_processor_MemoryPeakUsageProcessor::__invoke
     * @covers ehough_epilog_processor_MemoryProcessor::formatBytes
     */
    public function testProcessorWithoutFormatting()
    {
        $processor = new ehough_epilog_processor_MemoryPeakUsageProcessor(true, false);
        $record = $processor($this->getRecord());
        $this->assertArrayHasKey('memory_peak_usage', $record['extra']);
        $this->assertInternalType('int', $record['extra']['memory_peak_usage']);
        $this->assertGreaterThan(0, $record['extra']['memory_peak_usage']);
    }
}

<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TagProcessorTest extends ehough_epilog_TestCase
{
    /**
     * @covers ehough_epilog_processor_TagProcessor::__invoke
     */
    public function testProcessor()
    {
        $tags = array(1, 2, 3);
        $processor = new ehough_epilog_processor_TagProcessor($tags);
        $record = $processor($this->getRecord());
        
        $this->assertEquals($tags, $record['extra']['tags']);
    }
}

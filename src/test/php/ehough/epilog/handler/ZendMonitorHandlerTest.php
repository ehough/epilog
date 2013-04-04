<?php
/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class ehough_epilog_handler_ZendMonitorHandlerTest extends ehough_epilog_TestCase
{
    protected $zendMonitorHandler;

    public function setUp()
    {
        if (!function_exists('zend_monitor_custom_event')) {
            $this->markTestSkipped('ZendServer is not installed');
        }
    }

    /**
     * @covers  ehough_epilog_handler_ZendMonitorHandler::write
     */
    public function testWrite()
    {
        $record = $this->getRecord();
        $formatterResult = array(
            'message' => $record['message']
        );

        $zendMonitor = $this->getMockBuilder('ehough_epilog_handler_ZendMonitorHandler')
            ->setMethods(array('writeZendMonitorCustomEvent', 'getDefaultFormatter'))
            ->getMock();

        $formatterMock = $this->getMockBuilder('ehough_epilog_formatter_NormalizerFormatter')
            ->disableOriginalConstructor()
            ->getMock();

        $formatterMock->expects($this->once())
            ->method('format')
            ->will($this->returnValue($formatterResult));

        $zendMonitor->expects($this->once())
            ->method('getDefaultFormatter')
            ->will($this->returnValue($formatterMock));

        $levelMap = $zendMonitor->getLevelMap();

        $zendMonitor->expects($this->once())
            ->method('writeZendMonitorCustomEvent')
            ->with($levelMap[$record['level']], $record['message'], $formatterResult);

        $zendMonitor->handle($record);
    }

    /**
     * @covers ehough_epilog_handler_ZendMonitorHandler::getDefaultFormatter
     */
    public function testGetDefaultFormatterReturnsNormalizerFormatter()
    {
        $zendMonitor = new ZendMonitorHandler();
        $this->assertInstanceOf('ehough_epilog_formatter_NormalizerFormatter', $zendMonitor->getDefaultFormatter());
    }
}

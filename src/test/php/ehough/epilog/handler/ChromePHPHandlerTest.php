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
 * @covers ehough_epilog_handler_ChromePHPHandler
 */
class ehough_epilog_handler_ChromePHPHandlerTest extends ehough_epilog_TestCase
{
    protected function setUp()
    {
        ehough_epilog_handler_TestChromePHPHandler::reset();
    }

    public function testHeaders()
    {
        $handler = new ehough_epilog_handler_TestChromePHPHandler();
        $handler->setFormatter($this->getIdentityFormatter());
        $handler->handle($this->getRecord(ehough_epilog_Logger::DEBUG));
        $handler->handle($this->getRecord(ehough_epilog_Logger::WARNING));

        $expected = array(
            'X-ChromePhp-Data'   => base64_encode(utf8_encode(json_encode(array(
                'version' => ehough_epilog_handler_ChromePHPHandler::VERSION,
                'columns' => array('label', 'log', 'backtrace', 'type'),
                'rows' => array(
                    'test',
                    'test',
                ),
                'request_uri' => '',
            ))))
        );

        $this->assertEquals($expected, $handler->getHeaders());
    }

    public function testConcurrentHandlers()
    {
        $handler = new ehough_epilog_handler_TestChromePHPHandler();
        $handler->setFormatter($this->getIdentityFormatter());
        $handler->handle($this->getRecord(ehough_epilog_Logger::DEBUG));
        $handler->handle($this->getRecord(ehough_epilog_Logger::WARNING));

        $handler2 = new ehough_epilog_handler_TestChromePHPHandler();
        $handler2->setFormatter($this->getIdentityFormatter());
        $handler2->handle($this->getRecord(ehough_epilog_Logger::DEBUG));
        $handler2->handle($this->getRecord(ehough_epilog_Logger::WARNING));

        $expected = array(
            'X-ChromePhp-Data'   => base64_encode(utf8_encode(json_encode(array(
                'version' => ehough_epilog_handler_ChromePHPHandler::VERSION,
                'columns' => array('label', 'log', 'backtrace', 'type'),
                'rows' => array(
                    'test',
                    'test',
                    'test',
                    'test',
                ),
                'request_uri' => '',
            ))))
        );

        $this->assertEquals($expected, $handler2->getHeaders());
    }
}

class ehough_epilog_handler_TestChromePHPHandler extends ehough_epilog_handler_ChromePHPHandler
{
    protected $headers = array();

    public static function reset()
    {
        self::$initialized = false;
        self::$json['rows'] = array();
    }

    protected function sendHeader($header, $content)
    {
        $this->headers[$header] = $content;
    }

    public function getHeaders()
    {
        return $this->headers;
    }
}

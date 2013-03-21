<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//namespace Monolog\Handler;

//use Monolog\Logger;
//use Monolog\TestCase;

class NativeMailerHandlerTest extends ehough_epilog_TestCase
{
    /**
     * @expectedException InvalidArgumentException
     */
    public function testConstructorHeaderInjection()
    {
        $mailer = new ehough_epilog_handler_NativeMailerHandler('spammer@example.org', 'dear victim', "receiver@example.org\r\nFrom: faked@attacker.org");
    }
    
    /**
     * @expectedException InvalidArgumentException
     */
    public function testSetterHeaderInjection()
    {
        $mailer = new ehough_epilog_handler_NativeMailerHandler('spammer@example.org', 'dear victim', 'receiver@example.org');
        $mailer->addHeader("Content-Type: text/html\r\nFrom: faked@attacker.org");
    }
    
    /**
     * @expectedException InvalidArgumentException
     */
    public function testSetterArrayHeaderInjection()
    {
        $mailer = new ehough_epilog_handler_NativeMailerHandler('spammer@example.org', 'dear victim', 'receiver@example.org');
        $mailer->addHeader(array("Content-Type: text/html\r\nFrom: faked@attacker.org"));
    }
}

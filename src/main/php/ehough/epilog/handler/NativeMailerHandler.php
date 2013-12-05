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
 * NativeMailerHandler uses the mail() function to send the emails
 *
 * @author Christophe Coevoet <stof@notk.org>
 */
class ehough_epilog_handler_NativeMailerHandler extends ehough_epilog_handler_MailHandler
{
    protected $to;
    protected $subject;
    protected $headers = array(
        'Content-type: text/plain; charset=utf-8'
    );
    protected $maxColumnWidth;

    /**
     * @param string|array $to             The receiver of the mail
     * @param string       $subject        The subject of the mail
     * @param string       $from           The sender of the mail
     * @param integer      $level          The minimum logging level at which this handler will be triggered
     * @param boolean      $bubble         Whether the messages that are handled can bubble up the stack or not
     * @param int          $maxColumnWidth The maximum column width that the message lines will have
     */
    public function __construct($to, $subject, $from, $level = ehough_epilog_Logger::ERROR, $bubble = true, $maxColumnWidth = 70)
    {
        parent::__construct($level, $bubble);
        $this->to = is_array($to) ? $to : array($to);
        $this->subject = $subject;
        $this->addHeader(sprintf('From: %s', $from));
        $this->maxColumnWidth = $maxColumnWidth;
    }

    /**
     * @param string|array $headers Custom added headers
     */
    public function addHeader($headers)
    {
        foreach ((array) $headers as $header) {
            if (strpos($header, "\n") !== false || strpos($header, "\r") !== false) {
                throw new InvalidArgumentException('Headers can not contain newline characters for security reasons');
            }
            $this->headers[] = $header;
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function send($content, array $records)
    {
        $content = wordwrap($content, $this->maxColumnWidth);
        $headers = implode("\r\n", $this->headers) . "\r\n";
        foreach ($this->to as $to) {
            mail($to, $this->subject, $content, $headers);
        }
    }
}

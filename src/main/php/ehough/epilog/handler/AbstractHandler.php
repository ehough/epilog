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
 * Base Handler class providing the Handler structure
 *
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
abstract class ehough_epilog_handler_AbstractHandler implements ehough_epilog_handler_HandlerInterface
{
    protected $level = ehough_epilog_Logger::DEBUG;
    protected $bubble = true;

    /**
     * @var ehough_epilog_formatter_FormatterInterface
     */
    protected $formatter;
    protected $processors = array();

    /**
     * @param integer $level  The minimum logging level at which this handler will be triggered
     * @param Boolean $bubble Whether the messages that are handled can bubble up the stack or not
     */
    public function __construct($level = ehough_epilog_Logger::DEBUG, $bubble = true)
    {
        $this->level = $level;
        $this->bubble = $bubble;
    }

    /**
     * {@inheritdoc}
     */
    public function isHandling(array $record)
    {
        return $record['level'] >= $this->level;
    }

    /**
     * {@inheritdoc}
     */
    public function handleBatch(array $records)
    {
        foreach ($records as $record) {
            $this->handle($record);
        }
    }

    /**
     * Closes the handler.
     *
     * This will be called automatically when the object is destroyed
     */
    public function close()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function pushProcessor($callback)
    {
        if (!is_callable($callback) && !is_callable(array($callback, '__invoke'))) {
            throw new InvalidArgumentException('Processors must be valid callables (callback or object with an __invoke method), '.var_export($callback, true).' given');
        }
        array_unshift($this->processors, $callback);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function popProcessor()
    {
        if (!$this->processors) {
            throw new LogicException('You tried to pop from an empty processor stack.');
        }

        return array_shift($this->processors);
    }

    /**
     * {@inheritdoc}
     */
    public function setFormatter(ehough_epilog_formatter_FormatterInterface $formatter)
    {
        $this->formatter = $formatter;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFormatter()
    {
        if (!$this->formatter) {
            $this->formatter = $this->getDefaultFormatter();
        }

        return $this->formatter;
    }

    /**
     * Sets minimum logging level at which this handler will be triggered.
     *
     * @param  integer $level
     * @return self
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Gets minimum logging level at which this handler will be triggered.
     *
     * @return integer
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Sets the bubbling behavior.
     *
     * @param Boolean $bubble true means that this handler allows bubbling.
     *                        false means that bubbling is not permitted.
     * @return self
     */
    public function setBubble($bubble)
    {
        $this->bubble = $bubble;

        return $this;
    }

    /**
     * Gets the bubbling behavior.
     *
     * @return Boolean true means that this handler allows bubbling.
     *                 false means that bubbling is not permitted.
     */
    public function getBubble()
    {
        return $this->bubble;
    }

    public function __destruct()
    {
        try {
            $this->close();
        } catch (Exception $e) {
            // do nothing
        }
    }

    /**
     * Gets the default formatter.
     *
     * @return ehough_epilog_formatter_FormatterInterface
     */
    protected function getDefaultFormatter()
    {
        return new ehough_epilog_formatter_LineFormatter();
    }
}

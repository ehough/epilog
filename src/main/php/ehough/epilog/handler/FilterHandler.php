<?php

/**
 * Simple handler wrapper that filters records based on a list of levels
 *
 * It can be configured with an exact list of levels to allow, or a min/max level.
 *
 * @author Hennadiy Verkh
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
class ehough_epilog_handler_FilterHandler extends ehough_epilog_handler_AbstractHandler
{
    /**
     * Handler or factory callable($record, $this)
     *
     * @var callable|ehough_epilog_handler_HandlerInterface
     */
    protected $handler;

    /**
     * Minimum level for logs that are passes to handler
     *
     * @var int
     */
    protected $acceptedLevels;

    /**
     * Whether the messages that are handled can bubble up the stack or not
     *
     * @var Boolean
     */
    protected $bubble;

    private $_closure_setAcceptedLevels_minLevelOrList;
    private $_closure_setAcceptedLevels_maxLevel;


    /**
     * @param callable|ehough_epilog_handler_HandlerInterface $handler        Handler or factory callable($record, $this).
     * @param int|array                                       $minLevelOrList A list of levels to accept or a minimum level if maxLevel is provided
     * @param int                                             $maxLevel       Maximum level to accept, only used if $minLevelOrList is not an array
     * @param Boolean                                         $bubble         Whether the messages that are handled can bubble up the stack or not
     */
    public function __construct($handler, $minLevelOrList = ehough_epilog_Logger::DEBUG, $maxLevel = ehough_epilog_Logger::EMERGENCY, $bubble = true)
    {
        $this->handler  = $handler;
        $this->bubble   = $bubble;
        $this->setAcceptedLevels($minLevelOrList, $maxLevel);
    }

    /**
     * @return array
     */
    public function getAcceptedLevels()
    {
        return array_flip($this->acceptedLevels);
    }

    /**
     * @param int|array $minLevelOrList A list of levels to accept or a minimum level if maxLevel is provided
     * @param int       $maxLevel       Maximum level to accept, only used if $minLevelOrList is not an array
     */
    public function setAcceptedLevels($minLevelOrList = ehough_epilog_Logger::DEBUG, $maxLevel = ehough_epilog_Logger::EMERGENCY)
    {
        if (is_array($minLevelOrList)) {
            $acceptedLevels = $minLevelOrList;
        } else {
            $this->_closure_setAcceptedLevels_minLevelOrList = $minLevelOrList;
            $this->_closure_setAcceptedLevels_maxLevel = $maxLevel;
            $acceptedLevels = array_filter(ehough_epilog_Logger::getLevels(), array($this, '__callback_setAcceptedLevels'));
        }
        $this->acceptedLevels = array_flip($acceptedLevels);
    }

    public function __callback_setAcceptedLevels($level)
    {
        return $level >= $this->_closure_setAcceptedLevels_minLevelOrList && $level <= $this->_closure_setAcceptedLevels_maxLevel;
    }

    /**
     * {@inheritdoc}
     */
    public function isHandling(array $record)
    {
        return isset($this->acceptedLevels[$record['level']]);
    }

    /**
     * {@inheritdoc}
     */
    public function handle(array $record)
    {
        if (!$this->isHandling($record)) {
            return false;
        }

        // The same logic as in FingersCrossedHandler
        if (!$this->handler instanceof ehough_epilog_handler_HandlerInterface) {
            if (!is_callable($this->handler)) {
                throw new RuntimeException(
                    "The given handler (" . json_encode($this->handler)
                    . ") is not a callable nor a ehough_epilog_handler_HandlerInterface object"
                );
            }
            $this->handler = call_user_func($this->handler, $record, $this);
            if (!$this->handler instanceof ehough_epilog_handler_HandlerInterface) {
                throw new RuntimeException("The factory callable should return a ehough_epilog_handler_HandlerInterface");
            }
        }

        if ($this->processors) {
            foreach ($this->processors as $processor) {
                $record = call_user_func($processor, $record);
            }
        }

        $this->handler->handle($record);

        return false === $this->bubble;
    }

    /**
     * {@inheritdoc}
     */
    public function handleBatch(array $records)
    {
        $filtered = array();
        foreach ($records as $record) {
            if ($this->isHandling($record)) {
                $filtered[] = $record;
            }
        }

        $this->handler->handleBatch($filtered);
    }
}

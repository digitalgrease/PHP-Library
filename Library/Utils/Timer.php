<?php

/*
 * Copyright (c) 2014 Digital Grease Limited.
 * 
 * Tom Gray
 */

namespace DigitalGrease\Library\Utils;

require_once 'Timing.php';

/**
 * Timer to calculate intervals.
 * 
 * @author Tom Gray
 */
class Timer extends Timing
{
    
    /**
     * The start time in seconds since the Unix epoch accurate to the nearest
     * microsecond.
     * 
     * @var float
     */
    protected $start;
    
    /**
     * The end time in seconds since the Unix epoch accurate to the nearest
     * microsecond.
     * 
     * @var float
     */
    protected $end;
    
    /**
     * Construct the timer and set the start time.
     */
    public function __construct()
    {
        $this->start = microtime(true);
        $this->end = null;
    }
    
    /**
     * Get the end time of the interval.
     * 
     * @return float
     */
    public function end()
    {
        return $this->end;
    }
    
    /**
     * Get the number of microseconds between the start and the end time.
     * If the end time has not been set or the timer has been restarted since
     * the last time the end time was recorded, then this sets the end time and
     * returns the number of microseconds between the start time and now.
     * 
     * @param boolean $restart Restart the timer by setting the start time to
     *                         now if True.
     * 
     * @return float
     */
    public function getElapsedTime($restart = false)
    {
        if (empty($this->end) || $this->end < $this->start) {
            $this->end = microtime(true);
        }
        
        $interval = $this->end - $this->start;
        
        if ($restart) {
            $this->start = microtime(true);
        }
        
        return $interval;
    }
    
    /**
     * Get the time that has elapsed between the start and the end time as a
     * human readable string.
     * If the end time has not been set or the timer has been restarted since
     * the last time the end time was recorded, then this sets the end time and
     * returns the time elapsed between the start time and now.
     * 
     * @param boolean $restart Restart the timer by setting the start time to
     *                         now if True.
     * 
     * @return string
     */
    public function getElapsedTimeFormatted($restart = false)
    {
        return $this->formatSeconds($this->getElapsedTime($restart));
    }
    
    /**
     * Get the time elapsed to this point.
     * 
     * @return float
     */
    public function lap()
    {
        return microtime(true) - $this->start;
    }
    
    /**
     * Get the time elapsed to this point as a human readable string.
     * 
     * @return string
     */
    public function lapFormatted()
    {
        return $this->formatSeconds(microtime(true) - $this->start);
    }
    
    /**
     * Start the timer and set the start time of the interval.
     * 
     * @return float
     */
    public function start()
    {
        return $this->start = microtime(true);
    }
    
    /**
     * Stop the timer and set the end time of the interval.
     * 
     * @return float
     */
    public function stop()
    {
        return $this->end = microtime(true);
    }
}

<?php

/*
 * Copyright (c) 2014 Digital Grease Limited.
 * 
 * Tom Gray
 */

namespace GreasyLab\Library\Utils;

/**
 * Timer to calculate intervals.
 * 
 * @author Tom Gray
 */
class Timer
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
     * Set the end time of the interval.
     */
    public function end()
    {
        $this->end = microtime(true);
    }
    
    /**
     * Format the given number of seconds into a human readable string.
     * 
     * @param integer|float $seconds The number of seconds to be formatted which
     *                               may be a float that includes microseconds.
     * 
     * @return string
     */
    public function formatSeconds($seconds)
    {
        $decimalPointIndex = strrpos($seconds, '.');
        
        $microseconds = $decimalPointIndex === false
            ? 0
            : substr($seconds, $decimalPointIndex + 1);
        
        $mins = $seconds / 60;
        $hours = $mins / 60;
        $days = floor($hours / 24);
        
        return $days . ' days '
            . $hours % 24 . ' hours '
            . $mins % 60 . ' mins '
            . $seconds % 60 . ' secs '
            . $microseconds . ' ms';
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
     * Set the start time of the interval.
     */
    public function start()
    {
        $this->start = microtime(true);
    }
}

<?php

/*
 * Copyright (c) 2016 Digital Grease Limited.
 * 
 * Tuesday 8th November 2016
 * 
 * Tom Gray
 */

namespace DigitalGrease\Library\Utils;

require_once 'Timing.php';

/**
 * Stopwatch for timing.
 * 
 * @author Tom Gray
 */
class Stopwatch extends Timing
{
    
    /**
     * The start time in seconds since the Unix epoch accurate to the nearest
     * microsecond.
     * 
     * @var float
     */
    protected $start;
    
    /**
     * The split times in seconds since the Unix epoch accurate to the nearest
     * microsecond.
     * 
     * @var float[]
     */
    protected $splits;
    
    /**
     * Construct and start the stopwatch.
     */
    public function __construct()
    {
        $this->start();
    }
    
    /**
     * Get the time that has elapsed since the last time split was called or the
     * start if this is the first time split has been called.
     * 
     * @return float
     */
    public function split()
    {
        $split = microtime(true);
        $start = $this->splits
            ? $this->splits[count($this->splits) - 1]
            : $this->start;
        
        $this->splits[] = $split;
        return $split - $start;
    }
    
    /**
     * Start the stopwatch.
     * 
     * @return float
     */
    public function start()
    {
        $this->splits = [];
        return $this->start = microtime(true);
    }
    
    /**
     * Get the time that has elapsed since the stopwatch was started.
     * 
     * @return float
     */
    public function elapsed()
    {
        return microtime(true) - $this->start;
    }
}

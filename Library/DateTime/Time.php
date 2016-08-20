<?php

/*
 * Copyright (c) 2016 Digital Grease Limited.
 * 
 * Friday 22nd April 2016
 * 
 * Tom Gray
 */

namespace DigitalGrease\Library\DateTime;

/**
 * Represents a time with hour, minutes and seconds.
 * 
 * DO TG Time: Experiment: Add doctrine annotations and see if they are picked
 * up when used in a Symfony project. Does mapping need to be specified in the
 * project config?
 * 
 * @author Tom Gray
 */
class Time
{
    
    /**
     * The hour from 00-23.
     * 
     * @var int
     */
    protected $hour;
    
    /**
     * The number of minutes.
     * 
     * @var int
     */
    protected $mins;
    
    /**
     * The number of seconds.
     * 
     * @var int
     */
    protected $secs;
    
    /**
     * Construct a time.
     * 
     * @param string|int $hour
     * @param string|int $mins
     * @param string|int $secs
     * 
     * @throws \Exception
     */
    public function __construct($hour, $mins = '00', $secs = '00')
    {
        $this->hour = $this->validateHours($hour);
        $this->mins = $this->validateMinsSecs($mins);
        $this->secs = $this->validateMinsSecs($secs);
    }
    
    /**
     * Get a string representation of this time.
     * 
     * @return string
     */
    public function __toString()
    {
        return $this->hour . ':' . $this->mins . ':' . $this->secs;
    }
    
    /**
     * Get whether this time is after another time.
     * 
     * @param Time $anotherTime
     * 
     * @return bool
     */
    public function isAfter(Time $anotherTime)
    {
        return $this->__toString() > $anotherTime->__toString();
    }
    
    /**
     * Validate and format the hour value.
     * 
     * @param string|int $hour
     * 
     * @return string
     * 
     * @throws \Exception
     */
    protected function validateHours($hour)
    {
        // DO TG Time: Implement: Validate hour.
        return $hour;
    }
    
    /**
     * Validate and format the minutes and seconds value.
     * 
     * @param string|int $value
     * 
     * @return string
     * 
     * @throws \Exception
     */
    protected function validateMinsSecs($value)
    {
        // DO TG Time: Implement: Validate mins/secs.
        return $value;
    }
}

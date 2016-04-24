<?php

/*
 * Copyright (c) 2016 Greasy Lab.
 * 
 * Friday 22nd April 2016
 * 
 * Tom Gray
 */

namespace GreasyLab\Library\DateTime;

use GreasyLab\Library\DateTime\TimeRange;

/**
 * Defines a day and a time range to define something such as hours of business.
 *
 * @author Tom Gray
 */
class Hours
{
    
    /**
     * The day that these hours are defined for.
     * 
     * @var int
     */
    protected $day;
    
    /**
     * The hours defined for this day.
     * 
     * @var TimeRange
     */
    protected $timeRange;
    
    /**
     * Construct the hours for a day.
     * 
     * @param int $day
     * @param TimeRange $timeRange
     */
    public function __construct($day, TimeRange $timeRange)
    {
        $this->day = $day;
        $this->timeRange = $timeRange;
    }
    
    /**
     * Get the day these hours are defined for.
     * 
     * @return int
     */
    public function day()
    {
        return $this->day;
    }
    
    /**
     * Get whether the hours defined are for today.
     * 
     * @return bool
     */
    public function isToday()
    {
        return (new \DateTime())->format('N') == $this->day;
    }
    
    /**
     * Get whether the current day and time falls within these hours.
     * 
     * @return bool
     */
    public function isNowInHours()
    {
        return $this->isToday() && $this->timeRange->isNowInRange();
    }
}

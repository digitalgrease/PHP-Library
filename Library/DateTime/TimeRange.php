<?php

/*
 * Copyright (c) 2016 Greasy Lab.
 * 
 * Friday 22nd April 2016
 * 
 * Tom Gray
 */

namespace GreasyLab\Library\DateTime;

/**
 * Defines a time range.
 *
 * @author Tom Gray
 */
class TimeRange
{
    
    /**
     * The start of the time range.
     * 
     * @var Time
     */
    protected $start;
    
    /**
     * The end of the time range.
     * 
     * @var Time
     */
    protected $end;
    
    /**
     * Construct a new time range.
     * 
     * @param Time $start
     * @param Time $end
     * 
     * @throws \Exception
     */
    public function __construct(Time $start, Time $end)
    {
        if ($start->isAfter($end)) {
            throw new \Exception(
                'The end of a time range cannot be before the start.'
            );
        }
        $this->start = $start;
        $this->end = $end;
    }
    
    /**
     * Get whether the current time falls within this range.
     * 
     * @return bool
     */
    public function isNowInRange()
    {
        $now = (new \DateTime())->format('H:i:s');
        return $this->start->__toString() <= $now
            && $this->end->__toString() >= $now;
    }
}

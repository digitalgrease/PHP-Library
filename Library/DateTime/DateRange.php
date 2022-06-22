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
 * Defines a date range.
 *
 * @author Tom Gray
 */
class DateRange
{
    /**
     * The start of the date range.
     * 
     * @var \DateTime
     */
    protected $start;
    
    /**
     * The end of the date range.
     * 
     * @var \DateTime
     */
    protected $end;
    
    /**
     * Construct a new date range.
     * 
     * @param \DateTime $start The start of the date range.
     * @param \DateTime $end The end of the date range.
     * 
     * @throws \Exception
     */
    public function __construct(\DateTime $start, \DateTime $end)
    {
        if ($end < $start) {
            throw new \Exception(
                'The end of a date range cannot be before the start.'
            );
        }
    }
}

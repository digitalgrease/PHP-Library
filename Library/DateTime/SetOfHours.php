<?php

/*
 * Copyright (c) 2016 Greasy Labs.
 * 
 * Friday 22nd April 2016
 * 
 * Tom Gray
 */

namespace GreasyLab\Library\DateTime;

/**
 * Defines a set of hours for a week for something such as hours of business.
 *
 * @author Tom Gray
 */
class SetOfHours
{
    
    /**
     * The collection of hours in this set.
     * 
     * @var array
     */
    protected $hours;
    
    /**
     * Construct a set of hours.
     * 
     * @param array $hours
     * 
     * @throws \Exception
     */
    public function __construct(array $hours)
    {
        usort(
            $hours,
            function(Hours $a, Hours $b)
            {
                if ($a->day() > $b->day()) {
                    return 1;
                } elseif ($a->day() < $b->day()) {
                    return -1;
                } else {
                    return 0;
                }
            }
        );
        $this->hours = $hours;
    }
    
    /**
     * Get whether the time right now is currently within the hours defined.
     * 
     * @return bool
     */
    public function isNowInHours()
    {
        $isInHours = false;
        
        $i = 0;
        while ($i < count($this->hours) && !$isInHours) {
            $isInHours = $this->hours[$i]->isNowInHours();
            ++$i;
        }
        
        return $isInHours;
    }
}

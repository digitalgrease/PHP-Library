<?php

/*
 * Copyright (c) 2016 Greasy Labs.
 * 
 * Thursday 21st April 2016
 * 
 * Tom Gray
 */

namespace GreasyLab\Library\DateTime;

/**
 * Library utility to manage and work with a set of defined hours.
 *
 * @author Tom Gray
 */
class HoursManager
{
    
    /**
     * Get whether the time right now is currently within the hours defined.
     * 
     * @param array $hours
     * 
     * @return bool
     */
    public function isNowInHours(array $hours)
    {
        $isInHours = false;
        
        $now = new \DateTime();
        if (isset($hours[$now->format('N')])) {
            
        }
        
        return $isInHours;
    }
}

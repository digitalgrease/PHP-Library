<?php

/*
 * Copyright (c) 2016 Digital Grease Limited.
 * 
 * Tuesday 8th November 2016
 * 
 * Tom Gray
 */

namespace DigitalGrease\Library\Utils;

/**
 * Provides shared methods for objects that handle timing.
 * 
 * @author Tom Gray
 */
class Timing
{
    
    /**
     * Format the given number of seconds into a human readable string.
     * 
     * @param float $seconds The number of seconds to be formatted which may be
     *  a float that includes microseconds.
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
        
        $totalHours = $hours % 24;
        $totalMins = $mins % 60;
        $totalSeconds = $seconds % 60;
        
        $string = '';
        if ($days) {
            $string = $days . ' days ';
        }
        if ($totalHours) {
            $string .= $totalHours . ' hours ';
        }
        if ($totalMins) {
            $string .= $totalMins . ' mins ';
        }
        if ($totalSeconds) {
            $string .= $totalSeconds . ' secs ';
        }
        if ($microseconds) {
            $string .= $microseconds . ' ms';
        }
        
        return rtrim($string);
    }
}

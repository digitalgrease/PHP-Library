<?php

/*
 * Copyright (c) 2015 Greasy Labs.
 */

namespace GreasyLabs;

/**
 * Math utility functions.
 * 
 * @author The Janitor <thejanitor@greasylabs.com>
 */
class MathUtils
{
    /**
     * Compute and return a formatted percentage from the given values.
     * 
     * @param integer|float $value
     * @param integer|float $total
     * 
     * @return string
     */
    public static function computePercentage($value, $total)
    {
        if ($value == 0 && $total == 0) {
            $percentage = 100;
        } elseif ($value == 0) {
            $percentage = 0;
        } else {
            $percentage = ($value / $total) * 100;
        }
        return number_format($percentage, 2, '.', ',') . '%';
    }
}

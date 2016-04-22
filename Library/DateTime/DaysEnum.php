<?php

/*
 * Copyright (c) 2016 Greasy Lab.
 * 
 * Thursday 21st April 2016
 * 
 * Tom Gray
 */

namespace GreasyLab\Library\DateTime;

/**
 * Defines constants for the ISO-8601 numeric representations of the day of the
 * week (added in PHP 5.1.0).
 * 1 for Monday through to 7 for Sunday.
 * 
 * @author Tom Gray
 */
interface DaysEnum
{
    
    /**
     * The ISO-8601 numeric representation for Monday.
     * 
     * @var int
     */
    const MON = 1;
    
    /**
     * The ISO-8601 numeric representation for Tuesday.
     * 
     * @var int
     */
    const TUE = 2;
    
    /**
     * The ISO-8601 numeric representation for Wednesday.
     * 
     * @var int
     */
    const WED = 3;
    
    /**
     * The ISO-8601 numeric representation for Thursday.
     * 
     * @var int
     */
    const THU = 4;
    
    /**
     * The ISO-8601 numeric representation for Friday.
     * 
     * @var int
     */
    const FRI = 5;
    
    /**
     * The ISO-8601 numeric representation for Saturday.
     * 
     * @var int
     */
    const SAT = 6;
    
    /**
     * The ISO-8601 numeric representation for Sunday.
     * 
     * @var int
     */
    const SUN = 7;
}

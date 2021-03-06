<?php

/*
 * Copyright (c) 2016 Digital Grease Limited.
 * 
 * Thursday 21st April 2016
 * 
 * Tom Gray
 */

namespace DigitalGrease\Library\DateTime;

/**
 * Defines constants for the ISO-8601 numeric representations of the day of the
 * week (added in PHP 5.1.0).
 * 1 for Monday through to 7 for Sunday.
 * 
 * @author Tom Gray
 */
class DaysEnum
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
    
    /**
     * The days of the week.
     *
     * @var string[]
     */
    protected static $names = [
        self::MON => 'Monday',
        self::TUE => 'Tuesday',
        self::WED => 'Wednesday',
        self::THU => 'Thursday',
        self::FRI => 'Friday',
        self::SAT => 'Saturday',
        self::SUN => 'Sunday'
    ];
    
    /**
     * The ISO-8601 numeric values that represent the days of the week.
     *
     * @var int[]
     */
    protected static $values = [
        'Monday' => self::MON,
        'Tuesday' => self::TUE,
        'Wednesday' => self::WED,
        'Thursday' => self::THU,
        'Friday' => self::FRI,
        'Saturday' => self::SAT,
        'Sunday' => self::SUN
    ];
    
    /**
     * Get the names of the days of the week.
     * 
     * @return string[]
     */
    public static function getNames()
    {
        return self::$names;
    }
    
    /**
     * Get the ISO-8601 numeric values that represent the days of the week.
     * 
     * @return int[]
     */
    public static function getValues()
    {
        return self::$values;
    }
    
    /**
     * Get whether a date is a week day.
     * 
     * @param \DateTime $date
     * 
     * @return boolean
     */
    public static function isWeekDay(\DateTime $date)
    {
        return $date->format('N') >= self::MON
            && $date->format('N') <= self::FRI;
    }
}

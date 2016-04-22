<?php

/*
 * Copyright (c) 2016 Greasy Lab.
 * 
 * Friday 22nd April 2016
 * 
 * Tom Gray
 */

namespace GreasyLab\Library\Test\DateTime;

require_once '../DateTime/DateRange.php';

use GreasyLab\Library\DateTime\DateRange;

/**
 * Tests for the DateRange class.
 *
 * @author Tom Gray
 */
class DateRangeTest extends \PHPUnit_Framework_TestCase
{
    
    protected static $now;
    
    protected static $tomorrow;
    
    protected static $yesterday;
    
    /**
     * Create the fixtures used for the tests.
     * 
     * @return void
     */
    public static function setUpBeforeClass()
    {
        self::$now = new \DateTime();
        
        self::$tomorrow = new \DateTime();
        self::$tomorrow->modify('+1 day');
        
        self::$yesterday = new \DateTime();
        self::$yesterday->modify('-1 day');
    }
    
    /**
     * Test the constructor throws an exception when the end of a range is
     * before the start.
     * 
     * @test
     * @expectedException \Exception
     */
    public function throwExceptionWhenEndBeforeStart()
    {
        $dateRange = new DateRange(self::$tomorrow, self::$now);
    }
}

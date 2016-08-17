<?php

/*
 * Copyright (c) 2016 Digital Grease Limited.
 * 
 * Friday 22nd April 2016
 * 
 * Tom Gray
 */

namespace GreasyLab\Library\Test\DateTime;

require_once '../DateTime/Time.php';
require_once '../DateTime/TimeRange.php';

use GreasyLab\Library\DateTime\Time;
use GreasyLab\Library\DateTime\TimeRange;

/**
 * Tests for the TimeRange class.
 *
 * @author Tom Gray
 */
class TimeRangeTest extends \PHPUnit_Framework_TestCase
{
    
    /**
     *
     * @var Time
     */
    protected static $startOfDay;
    
    /**
     *
     * @var Time
     */
    protected static $midday;
    
    /**
     *
     * @var Time
     */
    protected static $endOfDay;
    
    /**
     * Create the fixtures used for the tests.
     * 
     * @return void
     */
    public static function setUpBeforeClass()
    {
        self::$startOfDay = new Time('09');
        self::$midday = new Time('12');
        self::$endOfDay = new Time('17');
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
        $timeRange = new TimeRange(self::$endOfDay, self::$startOfDay);
    }
}

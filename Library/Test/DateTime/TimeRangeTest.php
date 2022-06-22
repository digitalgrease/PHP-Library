<?php

/*
 * Copyright (c) 2016 Digital Grease Limited.
 * 
 * Friday 22nd April 2016
 * 
 * Tom Gray
 */

namespace DigitalGrease\Library\Test\DateTime;

use DigitalGrease\Library\DateTime\Time;
use DigitalGrease\Library\DateTime\TimeRange;
use Tests\TestCase;

/**
 * Tests for the TimeRange class.
 *
 * @author Tom Gray
 */
class TimeRangeTest extends TestCase
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
    public static function setUpBeforeClass(): void
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

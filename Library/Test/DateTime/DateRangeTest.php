<?php

/*
 * Copyright (c) 2016 Digital Grease Limited.
 * 
 * Friday 22nd April 2016
 * 
 * Tom Gray
 */

namespace DigitalGrease\Library\Test\DateTime;

use DigitalGrease\Library\DateTime\DateRange;
use Tests\TestCase;

/**
 * Tests for the DateRange class.
 *
 * @author Tom Gray
 */
class DateRangeTest extends TestCase
{
    /**
     *
     * @var \DateTime
     */
    protected static $now;
    
    /**
     *
     * @var \DateTime
     */
    protected static $tomorrow;
    
    /**
     *
     * @var \DateTime
     */
    protected static $yesterday;
    
    /**
     * Create the fixtures used for the tests.
     * 
     * @return void
     */
    public static function setUpBeforeClass(): void
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

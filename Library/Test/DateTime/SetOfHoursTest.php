<?php

/*
 * Copyright (c) 2016 Digital Grease Limited.
 * 
 * Friday 22nd April 2016
 * 
 * Tom Gray
 */

namespace DigitalGrease\Library\Test\DateTime;

require_once '../DateTime/DaysEnum.php';
require_once '../DateTime/Hours.php';
require_once '../DateTime/SetOfHours.php';
require_once '../DateTime/Time.php';
require_once '../DateTime/TimeRange.php';

use DigitalGrease\Library\DateTime\DaysEnum;
use DigitalGrease\Library\DateTime\Hours;
use DigitalGrease\Library\DateTime\SetOfHours;
use DigitalGrease\Library\DateTime\Time;
use DigitalGrease\Library\DateTime\TimeRange;

/**
 * Tests for the SetOfHours class.
 *
 * @author Tom Gray
 */
class SetOfHoursTest extends \PHPUnit_Framework_TestCase
{
    
    /**
     * Create the fixtures used for the tests.
     * 
     * @return void
     */
    public static function setUpBeforeClass()
    {
        
    }
    
    /**
     * Test the constructor throws an exception when something other than hours
     * is included in the array.
     * 
     * @test
     * @expectedException \Exception
     */
    public function constructorThrowsExceptionWhenDataInvalid()
    {
        $setOfHours = new SetOfHours(
            [
                new Hours(
                    DaysEnum::WED, new TimeRange(new Time('09'), new Time('17'))
                ),
                new Hours(
                    DaysEnum::SAT, new TimeRange(new Time('09'), new Time('17'))
                ),
                2,
                new Hours(
                    DaysEnum::MON, new TimeRange(new Time('09'), new Time('17'))
                )
            ]
        );
    }
    
    /**
     * Test that the current date and time is correctly determined to be inside
     * and outside of a set of hours.
     * 
     * @test
     */
    public function isNowInHoursCorrectlyDeterminesDateTimeInHours()
    {
        $now = new \DateTime();
        
        $setOfHours = new SetOfHours(
            [
                new Hours(
                    $now->format('N'),
                    new TimeRange(
                        new Time($now->format('H')),
                        new Time($now->format('H') + 1)
                    )
                )
            ]
        );
        
        $this->assertTrue($setOfHours->isNowInHours());
    }
}

<?php

/*
 * Copyright (c) 2015 Digital Grease Limited.
 * 
 * Tom Gray
 * 
 * Date: 9th October 2015
 */

namespace DigitalGrease\Library\Data;

/**
 * Data cleanser that cleans telephone numbers.
 * 
 * @version 1.0 9th October 2015
 * @author Tom Gray
 * @copyright 2015 Digital Grease Limited
 */
class TelephoneCleanser implements DataCleanserInterface
{
    /**
     * Currently strips spaces from telephone numbers.
     * 
     * DO TG Implement: Cleanse phone numbers:
     * - 11 digits?
     * - no +
     * - no ()
     * - no / and aplit multi numbers
     * 
     * @param string $number
     * 
     * @return array
     */
    public function cleanse($number)
    {
        return preg_replace('/\s+/', '', $number);
    }
}

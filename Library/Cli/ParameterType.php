<?php

/*
 * Copyright (c) 2015 Digital Grease Limited.
 * 
 * Monday 16th November 2015
 * 
 * Tom Gray
 */

namespace DigitalGrease\Library\Cli;

/**
 * Defines a set of parameter types a CLI command can have.
 *
 * @author Tom Gray
 * @version 1.0 Monday 16th November 2015
 */
class ParameterType
{
    
    /**
     * The argument must be a valid file.
     * 
     * @var int
     */
    const INPUT_FILE = 1;
    
    /**
     * The argument must be a valid directory.
     * 
     * @var int
     */
    const INPUT_DIR = 2;
    
    /**
     * The argument must be a URL.
     * 
     * @var int
     */
    const URL = 3;
    
    /**
     * The argument must be a valid output file path.
     * 
     * @var int
     */
    const OUTPUT_FILE = 4;
    
    /**
     * The argument must be a valid year of two digits.
     * 
     * @var int
     */
    const YEAR_2_DIGITS = 5;
    
    /**
     * The argument must be a valid year of four digits.
     * 
     * @var int
     */
    const YEAR_4_DIGITS = 6;
    
    /**
     * The argument must be a valid integer.
     * 
     * @var int
     */
    const INT = 7;
    
    /**
     * The argument must be a string.
     * 
     * @var int
     */
    const STRING = 8;
    
    /**
     * The argument must be a real number.
     * 
     * @var int
     */
    const DOUBLE = 9;
    
    /**
     * The argument must be a valid output directory path.
     * 
     * @var int
     */
    const OUTPUT_DIR = 10;
    
    /**
     * The argument must be a valid date and time in the ISO format
     * "Y-m-d H:i:s".
     * 
     * @var int
     */
    const DATE_TIME = 11;
}

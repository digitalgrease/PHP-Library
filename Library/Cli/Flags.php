<?php

/*
 * Copyright (c) 2015 Digital Grease Limited.
 * 
 * Wednesday 25th November 2015
 * 
 * Tom Gray
 */

namespace GreasyLab\Library\Cli;

require_once 'Flag.php';

use GreasyLab\Library\Cli\Flag;

/**
 * Defines a collection of command line flags.
 *
 * @author Tom Gray
 * @version 1.0 Wednesday 25th November 2015
 */
class Flags
{
    /**
     * The error message if any flags fail validation.
     * 
     * @var string
     */
    protected $error = '';
    
    /**
     * The defined flags.
     * 
     * @var array
     */
    protected $flags;
    
    /**
     * Construct a collection of command line flags.
     */
    public function __construct()
    {
        $this->flags = [];
    }
    
    /**
     * Define and add a new flag to this collection.
     * 
     * @param string $char
     * @param string $description
     * 
     * @return Flags This collection to allow method chaining.
     */
    public function add($char, $description)
    {
        $this->flags[$char] = new Flag($char, $description);
        return $this;
    }
    
    /**
     * Get the last error message.
     * 
     * @return string
     */
    public function error()
    {
        return $this->error;
    }
    
    /**
     * Get all the flags that have been defined.
     * 
     * @return array
     */
    public function getFlags()
    {
        return $this->flags;
    }
    
    /**
     * Get whether any flags have been defined.
     * 
     * @return boolean
     */
    public function hasFlags()
    {
        return count($this->flags) != 0;
    }
    
    /**
     * Get whether a flag has been set.
     * 
     * @param string $char
     * 
     * @return boolean
     */
    public function isFlagOn($char)
    {
        return $this->flags[$char]->isOn();
    }
    
    /**
     * Get whether a string is a flag string.
     * 
     * @param string $string
     * 
     * @return boolean
     */
    public function isFlagString($string)
    {
        return $string[0] == '-';
    }
    
    /**
     * Get whether a string of characters are valid flags and turn the flags on.
     * 
     * @param string $flags The flag characters.
     * 
     * @return boolean True if the characters fit the defined flags, false
     *  otherwise.
     */
    public function isValidFlagString($flags)
    {
        for ($i = 1; $i < strlen($flags); ++$i) {
            if (array_key_exists($flags[$i], $this->flags)) {
                $this->flags[$flags[$i]]->setIsOn();
            } else {
                $this->error = '"'.$flags[$i].'" is not a valid flag';
                return false;
            }
        }
        return $this->isFlagString($flags);
    }
    
    /**
     * Get the number of flags defined in this collection.
     * 
     * @return int
     */
    public function size()
    {
        return count($this->flags);
    }
}

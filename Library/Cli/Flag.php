<?php

/*
 * Copyright (c) 2015 Greasy Lab.
 * 
 * Wednesday 25th November 2015
 * 
 * Tom Gray
 */

namespace GreasyLab\Library\Cli;

/**
 * Defines a command line flag.
 *
 * @author Tom Gray
 * @version 1.0 Wednesday 25th November 2015
 */
class Flag
{
    /**
     * The character that represents this flag.
     * 
     * @var string
     */
    protected $char;
    
    /**
     * The description of this flag.
     * 
     * @var string
     */
    protected $description;
    
    /**
     * The value of this flag.
     * 
     * @var boolean
     */
    protected $isOn;
    
    /**
     * Construct a flag.
     * 
     * @param string $char The character that represents this flag.
     * @param string $description The description of this flag.
     */
    public function __construct($char, $description)
    {
        $this->char = $char;
        $this->description = $description;
        $this->isOn = false;
    }
    
    /**
     * Get the character that represents this flag.
     * 
     * @return string
     */
    public function char()
    {
        return $this->char;
    }
    
    /**
     * Get the description of this flag.
     * 
     * @return string
     */
    public function description()
    {
        return $this->description;
    }
    
    /**
     * Get whether this flag has been set.
     * 
     * @return boolean
     */
    public function isOn()
    {
        return $this->isOn;
    }
    
    /**
     * Set the value of this flag.
     * 
     * @param boolean $isOn
     * 
     * @return Flag This flag to allow method chaining.
     */
    public function setIsOn($isOn = true)
    {
        $this->isOn = $isOn;
        return $this;
    }
}

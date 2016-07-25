<?php

/*
 * Copyright (c) Greasy Lab.
 * 
 * Monday 25th July 2016
 * 
 * Tom Gray
 */

namespace GreasyLab\Library\Data;

/**
 * Value object for representing a primary key.
 *
 * @author Tom Gray
 */
class PrimaryKey
{
    
    /**
     * The reference of the primary key being represented.
     * 
     * @var string
     */
    protected $primaryKey;
    
    /**
     * Construct a primary key.
     * 
     * @param string $key
     */
    public function __construct($key)
    {
        $this->primaryKey = $key;
    }
    
    /**
     * Get the reference of the primary key being represented.
     * 
     * @return string
     */
    public function primaryKey()
    {
        return $this->primaryKey;
    }
}

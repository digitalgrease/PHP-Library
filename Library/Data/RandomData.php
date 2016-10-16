<?php

/*
 * Copyright (c) Digital Grease Limited.
 * 
 * Thursday 29th September 2016
 * 
 * Tom Gray
 */

namespace DigitalGrease\Library\Data;

/**
 * Value object to specify random data to be created when importing a DataSet
 * into a database.
 *
 * @author Tom Gray
 */
class RandomData
{
    
    /**
     * Constant to represent a data type of an MD5 hash.
     * 
     * @var int
     */
    const MD5 = 0;
    
    /**
     * The type of this random data.
     * 
     * @var int
     */
    protected $dataType;
    
    /**
     * Construct a random data value object.
     * 
     * @param int $type
     */
    public function __construct($type)
    {
        $this->dataType = $type;
    }
    
    /**
     * Get the data type of this random data.
     * 
     * @return int
     */
    public function dataType()
    {
        return $this->dataType;
    }
    
    /**
     * Generate random data.
     * 
     * @return string
     */
    public function generateData()
    {
        switch ($this->dataType) {
            case self::MD5 :
                return md5(uniqid('', true));
            default :
                throw new \Exception(
                    '"' . $this->dataType . '" is not a valid data type.'
                );
        }
    }
}

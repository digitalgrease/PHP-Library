<?php

/*
 * Copyright (c) Digital Grease Limited.
 * 
 * Monday 3rd October 2016
 * 
 * Tom Gray
 */

namespace DigitalGrease\Library\Data;

/**
 * Value object for representing a conditional mapping on a column in a DataSet.
 *
 * @author Tom Gray
 */
class DataSetColumnConditional
{
    
    /**
     * The column the conditional mapping applies to.
     * 
     * @var int|string
     */
    protected $column;
    
    /**
     * The conditional mapping to be applied to the column.
     * 
     * @var array
     */
    protected $mapping;
    
    /**
     * Construct a data set column conditional mapping.
     * 
     * @param int|string $column
     * @param array $mapping
     */
    public function __construct($column, array $mapping)
    {
        $this->column = $column;
        $this->mapping = $mapping;
    }
    
    /**
     * Get the column being represented.
     * 
     * @return int|string
     */
    public function column()
    {
        return $this->column;
    }
    
    /**
     * Get the conditional mapping to apply to the column.
     * 
     * @return array
     */
    public function mapping()
    {
        return $this->mapping;
    }
}

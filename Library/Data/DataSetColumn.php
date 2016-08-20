<?php

/*
 * Copyright (c) Digital Grease Limited.
 * 
 * Monday 25th July 2016
 * 
 * Tom Gray
 */

namespace DigitalGrease\Library\Data;

/**
 * Value object for representing a column in a DataSet.
 *
 * @author Tom Gray
 */
class DataSetColumn
{
    
    /**
     * The column being represented.
     * 
     * @var int|string
     */
    protected $column;
    
    /**
     * Construct a data set column.
     * 
     * @param int|string $column
     */
    public function __construct($column)
    {
        $this->column = $column;
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
}

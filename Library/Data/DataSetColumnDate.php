<?php

/*
 * Copyright (c) Digital Grease Limited.
 * 
 * Tuesday 4th October 2016
 * 
 * Tom Gray
 */

namespace DigitalGrease\Library\Data;

/**
 * Value object for representing and manipulating a column in a DataSet that
 * contains a date.
 *
 * @author Tom Gray
 */
class DataSetColumnDate
{
    
    /**
     * Do not modify the date.
     * 
     * @var int
     */
    const NO_MODIFIER = 0;
    
    /**
     * Modify the date by adding years to find the next occurrence of the date
     * and month from today.
     * 
     * @var int
     */
    const NEXT_OCCURRENCE = 1;
    
    /**
     * The column being represented that contains the date.
     * 
     * @var int|string
     */
    protected $column;
    
    /**
     * The format of the date stored in the column.
     * 
     * @var string
     */
    protected $columnFormat;
    
    /**
     * Defines whether and how the date should be modified.
     * 
     * @var int
     */
    protected $modifier;
    
    /**
     * The format to return the date in.
     * 
     * @var string
     */
    protected $returnFormat;
    
    /**
     * Construct a data set date.
     * 
     * @param int|string $column
     * @param string $columnFormat
     * @param string $returnFormat
     * @param int $modifier
     */
    public function __construct(
        $column,
        $columnFormat,
        $returnFormat,
        $modifier = self::NONE
    ) {
        $this->column = $column;
        $this->columnFormat = $columnFormat;
        $this->returnFormat = $returnFormat;
        $this->modifier = $modifier;
    }
    
    /**
     * {@inheritdoc}
     */
    public function setValue(array $row, $field, array &$data)
    {
        if ($row[$this->column]) {
            
            $date = \DateTime::createFromFormat(
                $this->columnFormat,
                $row[$this->column]
            );
            
            switch ($this->modifier) {
                case self::NO_MODIFIER:
                    break;
                case self::NEXT_OCCURRENCE:
                    $today = new \DateTime();
                    while ($date->format('Y-m-d') < $today->format('Y-m-d')) {
                        $date->add(new \DateInterval('P1Y'));
                    }
                    break;
            }
            
            $data[$field] = $date->format($this->returnFormat);
            return true;
            
        } else {
            return false;
        }
    }
}

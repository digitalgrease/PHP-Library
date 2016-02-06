<?php

/*
 * Copyright (c) 2015 Greasy Lab.
 * 
 * Tom Gray
 * 
 * Date: 8th October 2015
 */

namespace GreasyLab\Library\Data;

/**
 * API for a repository.
 * 
 * @version 1.0 8th October 2015
 * @author Tom Gray
 * @copyright 2015 Greasy Lab
 */
abstract class AbstractDatabase
{
    /**
     * DO TG Comment
     * 
     * @return boolean True on success.
     * 
     * @throws \Exception
     */
    abstract public function commit();
    
    /**
     * Delete a record.
     * Using $recurse = true finds and removes all related child rows through
     * foreign key constraints that are preventing the removal of any original
     * records.
     * 
     * DO TG Further Dev: Get a list of all the statements that will be run, in
     * order.
     * 
     * @param string  $table
     * @param array   $criteria
     * @param boolean $recurse
     * 
     * @return boolean
     * 
     * @throws \Exception
     */
    abstract public function delete($table, array $criteria, $recurse = false);
    
    /**
     * DO TG Comment
     * 
     * @param string $table
     * @param string $column
     * 
     * @return int|null
     * 
     * @throws \Exception
     */
    abstract public function findHighestValue($table, $column);
    
    /**
     * Get data from a native query.
     * 
     * @param string $query
     * 
     * @return DataSet 
     * 
     * @throws \Exception
     */
    abstract public function getQueryDataset($query);
    
    /**
     * Get data from a single table.
     * 
     * @param string $table
     * @param array  $criteria
     * 
     * @return DataSet 
     * 
     * @throws \Exception
     */
    abstract public function getTableDataset($table, array $criteria = []);
    
    /**
     * 
     * 
     * @param string $table
     * 
     * @return DataSet
     * 
     * @throws \Exception
     */
    abstract public function getTableMetaData($table);
    
    /**
     * 
     * 
     * @param string $table
     * @param array  $criteria
     * 
     * @return boolean
     * 
     * @throws \Exception
     */
    abstract public function hasRows($table, array $criteria = []);
    
    /**
     * 
     * 
     * @param string $table
     * @param array  $criteria
     * 
     * @return boolean
     * 
     * @throws \Exception
     */
    abstract public function hasSingleRow($table, array $criteria = []);
    
    /**
     * Insert the data.
     * 
     * @param string $table
     * @param array  $data
     * 
     * @return int
     */
    abstract public function insert($table, array $data);
    
    /**
     * Insert data and ignore existing rows.
     * 
     * @param string $table
     * @param array  $data
     * 
     * @return int ?? Behaviour on ignore??
     * 
     * @throws \Exception
     */
    abstract public function insertIgnore($table, array $data);

    /**
     * Insert data and update existing rows.
     * 
     * @param string $table
     * @param array  $data
     * 
     * @return int ?? Behaviour on update??
     * 
     * @throws \Exception
     */
    abstract public function insertUpdate($table, array $data);
    
    /**
     * 
     */
    abstract public function startTransaction();
    
    /**
     * Truncate a table.
     * 
     * @param string $table
     * 
     * @throws \Exception
     */
    abstract public function truncate($table);
    
    /**
     * Set foreign key checks to false.
     * 
     * @throws \Exception
     */
    abstract public function turnOffForeignKeyChecks();

    /**
     * Set foreign key checks to true.
     * 
     * @throws \Exception
     */
    abstract public function turnOnForeignKeyChecks();
    
    /**
     * DO TG Comment
     * 
     * @param string $table
     * @param array $values
     * 
     * @return boolean
     */
    abstract public function update($table, array $values);
    
    /**
     * Get whether a value is either null or the string "null".
     * 
     * @param mixed $value
     * 
     * @return boolean
     */
    protected function isNull($value)
    {
        return $value === null || strtolower($value) === 'null';
    }
}

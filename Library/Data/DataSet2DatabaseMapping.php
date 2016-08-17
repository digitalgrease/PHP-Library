<?php

/*
 * Copyright (c) Digital Grease Limited.
 * 
 * Monday 25th July 2016
 * 
 * Tom Gray
 */

namespace GreasyLab\Library\Data;

/**
 * Initial implementation of the class to import data from a DataSet into a
 * database.
 * 
 * @author Tom Gray
 */
class DataSet2DatabaseMapping
{
    
    /**
     * Import data from a DataSet into a database using a mapping.
     * IMPORTANT: Currently the mapping must be defined in insertion order so
     *  that all requried primary keys are available when they are required.
     * 
     * DO TG Feature/Improvement: Create a value object for the mapping.
     * DO TG Feature/Improvement: Order the mappings based on when a PK is
     *  required.
     * DO TG Feature/Improvement: Insert records in one query where the PK is
     *  not required for subsequent insertions/mappings.
     * 
     * @param DataSet $dataSet
     * @param AbstractDatabase $db
     * @param array $mappings
     * 
     * @return void
     */
    public function import(
        DataSet $dataSet,
        AbstractDatabase $db,
        array $mappings
    ) {
        $primaryKeys = [];
        
        foreach ($mappings as $mapping) {
            
            if (isset($primaryKeys[$mapping['reference']])) {
                throw new \Exception(
                    'Mapping reference must be unique - "'
                    . $mapping['reference'] . '" has been used twice.'
                );
            } else {
                $primaryKeys[$mapping['reference']] = [];
            }
            
            if ($this->mappingContainsDataSetColumn($mapping['fields'])) {
                foreach ($dataSet->getRows() as $iRow => $row) {

                    // Build the data to be inserted from the mapping.
                    $data = [];
                    $rowContainsData = false;
                    foreach ($mapping['fields'] as $field => $value) {
                        if ($value instanceof DataSetColumn) {
                            if ($row[$value->column()]) {
                                $data[$field] = $row[$value->column()];
                                $rowContainsData = true;
                            }
                        } elseif ($value instanceof PrimaryKey) {
                            if (count($primaryKeys[$value->primaryKey()]) > 1) {
                                $data[$field] = $primaryKeys[$value->primaryKey()][$iRow];
                            } else {
                                $data[$field] = $primaryKeys[$value->primaryKey()][0];
                            }
                        } else {
                            $data[$field] = $value;
                        }
                    }
                    
                    if ($rowContainsData) {
                        $primaryKeys[$mapping['reference']][$iRow] =
                            $db->insert($mapping['table'], [$data]);
                    } else {
                        $primaryKeys[$mapping['reference']][$iRow] = null;
                    }
                }
                
            } elseif ($this->mappingContainsPrimaryKey($mapping['fields'])) {
                $reference = $this->getLargestPrimaryKeyCollection($mapping['fields'], $primaryKeys);
                
                foreach ($primaryKeys[$reference] as $iRow => $pk) {
                    
                    // Build the data to be inserted from the mapping.
                    $data = [];
                    $rowContainsData = true;
                    foreach ($mapping['fields'] as $field => $value) {
                        if ($value instanceof PrimaryKey) {
                            if (count($primaryKeys[$value->primaryKey()]) > 1) {
                                if ($primaryKeys[$value->primaryKey()][$iRow]) {
                                    $data[$field] = $primaryKeys[$value->primaryKey()][$iRow];
                                } else {
                                    $rowContainsData = false;
                                }
                            } else {
                                if ($primaryKeys[$value->primaryKey()][0]) {
                                    $data[$field] = $primaryKeys[$value->primaryKey()][0];
                                } else {
                                    $rowContainsData = false;
                                }
                            }
                        } else {
                            $data[$field] = $value;
                        }
                    }
                    
                    if ($rowContainsData) {
                        $primaryKeys[$mapping['reference']][$iRow] =
                            $db->insert($mapping['table'], [$data]);
                    } else {
                        $primaryKeys[$mapping['reference']][$iRow] = null;
                    }
                }
                
            } else {
                
                // Build the data to be inserted from the mapping.
                $data = [];
                foreach ($mapping['fields'] as $field => $value) {
                    $data[$field] = $value;
                }
                $primaryKeys[$mapping['reference']][0] = $db->insert($mapping['table'], [$data]);
            }
        }
        
        file_put_contents('primaryKeys', print_r($primaryKeys, true));
    }
    
    /**
     * 
     * 
     * @return bool
     */
    protected function mappingContainsDataSetColumn($mapping)
    {
        foreach ($mapping as $value) {
            if ($value instanceof DataSetColumn) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * 
     * 
     * @return bool
     */
    protected function mappingContainsPrimaryKey($mapping)
    {
        foreach ($mapping as $value) {
            if ($value instanceof PrimaryKey) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * 
     * 
     * @return string
     */
    protected function getLargestPrimaryKeyCollection($mapping, $primaryKeys)
    {
        $largest = '';
        $max = 0;
        
        foreach ($mapping as $value) {
            if ($value instanceof PrimaryKey) {
                if (count($primaryKeys[$value->primaryKey()]) > $max) {
                    $max = count($primaryKeys[$value->primaryKey()]);
                    $largest = $value->primaryKey();
                }
            }
        }
        
        return $largest;
    }
}

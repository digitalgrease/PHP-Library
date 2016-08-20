<?php

/*
 * Copyright (c) 2015 Digital Grease Limited.
 * 
 * Tuesday 3rd November 2015
 * 
 * Tom Gray
 */

namespace DigitalGrease\Library\Utils;

/**
 * Collects collections of distinct data and writes them to disk.
 * 
 * @author Tom Gray
 */
class DataLog
{
    
    /**
     * Collection of the data.
     * 
     * @var array
     */
    protected $data;
    
    /**
     * Path to the data directory that the collection files are written to.
     * 
     * @var string
     */
    protected $dataDirectory;
    
    /**
     * Create a data log.
     * 
     * @param string $dataDirectory Path to the data directory that the
     *  collection files are written to.
     * 
     * @throws \Exception
     */
    public function __construct($dataDirectory)
    {
        $this->data = [];
        $this->dataDirectory = rtrim($dataDirectory, '/') . '/';
        if (!is_dir($this->dataDirectory)) {
            if (!mkdir($this->dataDirectory, 0744, true)) {
                throw new \Exception(
                    'Data log directory does not exist and cannot be created'
                );
            }
        }
    }
    
    /**
     * Add data to a collection.
     * 
     * @param string $collection
     * @param string $data
     * 
     * @return void
     */
    public function add($collection, $data)
    {
        $value = (string) $data;
        
        if (
            !isset($this->data[$collection])
            || !isset($this->data[$collection][$value])
        ) {
            $this->data[$collection][$value] = 1;
        } else {
            ++$this->data[$collection][$value];
        }
        
        ksort($this->data[$collection]);
    }
    
    /**
     * Get a collection of data.
     * 
     * @param string $collection The name of the collection.
     * 
     * @return array The data in the collection.
     */
    public function get($collection)
    {
        if (isset($this->data[$collection])) {
            return $this->data[$collection];
        } else {
            return [];
        }
    }
    
    /**
     * Write the collections to disk.
     * 
     * @return void
     */
    public function save()
    {
        foreach ($this->data as $collection => $data) {
            $output = '';
            foreach ($data as $value => $count) {
                $output .= $value . ' ' . $count . PHP_EOL;
            }
            file_put_contents(
                $this->dataDirectory . $collection . '.log',
                $output
            );
        }
    }
}

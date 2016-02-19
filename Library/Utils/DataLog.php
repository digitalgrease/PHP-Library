<?php

/*
 * Copyright (c) 2015 Greasy Lab.
 * 
 * Tuesday 3rd November 2015
 * 
 * Tom Gray
 */

namespace GreasyLab\Library\Utils;

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
     * Add data to a collection and update the collection on disk.
     * 
     * @param string $collection
     * @param string $data
     * 
     * @return void
     */
    public function add($collection, $data)
    {
        if (
            !isset($this->data[$collection])
            || !isset($this->data[$collection][$data])
        ) {
            $this->data[$collection][$data] = 1;
        } else {
            ++$this->data[$collection][$data];
        }
        
        ksort($this->data[$collection]);
        $output = '';
        foreach ($this->data[$collection] as $value => $count) {
            $output .= $value . ' ' . $count . PHP_EOL;
        }
        file_put_contents(
            $this->dataDirectory . $collection . '.log',
            $output
        );
    }
}

<?php

/*
 * Copyright (c) 2016 Digital Grease Limited.
 * 
 * Thursday 10th November 2016
 * 
 * Tom Gray
 */

namespace DigitalGrease\Library\Utils;

/**
 * Utility methods to manipulate arrays.
 *
 * @author Tom Gray
 */
class ArrayUtils
{
    
    /**
     * Create an array of all unique combinations of elements that can be
     * created from the elements of multiple arrays.
     * This is a variadic function that will work with two or more arrays.
     * 
     * @param array $array1
     * @param array $array2
     * 
     * @return array
     */
    public function createCombinations(array $array1, array $array2)
    {
        $combinations = [];
        
        $totalCombinations = 1;
        foreach (func_get_args() as $array) {
            $totalCombinations *= count($array);
        }
        
        // Initialise the indexes for accessing the arrays to build the
        // combinations.
        $indexes = [];
        for ($i = 0; $i < func_num_args(); ++$i) {
            $indexes[] = 0;
        }
        
        for ($i = 0; $i < $totalCombinations; ++$i) {
            $elements = [];
            foreach (func_get_args() as $a => $array) {
                $elements[] = $array[$indexes[$a]];
            }
            $combinations[] = $elements;
            
            // Set the next index to create the next combination.
            foreach (func_get_args() as $a => $array) {
                if (++$indexes[$a] < count($array)) {
                    break;
                }
                $indexes[$a] = 0;
            }
        }
        
        return $combinations;
    }
    
    /**
     * Build an XML string of a single order to be posted to the API from the
     * data structure.
     * 
     * @param array $data
     * 
     * @return string
     */
    public function dataToXml(array $data)
    {
        $xml = new \SimpleXMLElement('<?xml version="1.0"?><CustomerOrder></CustomerOrder>');
        
        $this->arrayToXml($data, $xml);
        
        return $xml->asXML();
    }
    
    /**
     * 
     * @param array $array
     * @param \SimpleXMLElement $node
     * 
     * @return void
     */
    protected function arrayToXml(array $array, \SimpleXMLElement &$xml)
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                if (!is_numeric($key)) {
                    $subnode = $xml->addChild("$key");
                    $this->arrayToXml($value, $subnode);
                } else {
                    $this->arrayToXml($value, $xml);
                }
            } else {
                $xml->addChild("$key","$value");
            }
        }
    }
}

<?php

/*
 * Copyright (c) 2016 Digital Grease Limited.
 * 
 * Friday 27th May 2016
 * 
 * Tom Gray
 */

namespace GreasyLab\Library\Files;

/**
 * Provides generic methods for parsing files.
 *
 * @author Tom Gray
 */
class Parser
{
    
    /**
     * Parse a file containing key value pairs.
     * 
     * @param string $filePath
     * @param string $delimiter
     * 
     * @return array
     */
    public function parseKeyValuePairs($filePath, $delimiter = '=')
    {
        $handle = fopen($filePath, 'r');
        
        $fileContent = [];
        $key = '';
        $value = '';
        $isFirstPass = true;
        
        while (!feof($handle)) {
            
            // Read in the start of a new line.
            $char = fgetc($handle);
            $tmp = '';
            
            while (!feof($handle) && $char != $delimiter && $char != PHP_EOL) {
                $tmp .= $char;
                $char = fgetc($handle);
            }
            
            // If this new line contains a key then save it.
            if ($char == $delimiter) {
                
                // If this is the first pass then there will be no value read
                // yet, otherwise the last value has just been read and needs to
                // be saved.
                if ($isFirstPass) {
                    $isFirstPass = false;
                } else {
                    $fileContent[] = [
                        'key' => strtolower(trim($key)),
                        'value' => trim($value)
                    ];
                }
                $key = $tmp;
                $value = '';
            } else {
                
                // This new line does not contain a key so it is still part of
                // the value to be saved.
                $value .= $tmp . $char;
            }
        }
        
        // The end of the file has been reached and the last key and value pair
        // read needs to be saved.
        $fileContent[] = [
            'key' => strtolower(trim($key)),
            'value' => trim($value)
        ];
        
        fclose($handle);
        
        return $fileContent;
    }
}

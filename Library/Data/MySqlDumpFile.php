<?php

/*
 * Copyright (c) 2015 Digital Grease Limited.
 * 
 * Thursday 3rd December 2015
 * 
 * Tom Gray
 */

namespace GreasyLab\Library\Data;

require_once 'DataSet.php';

use GreasyLab\Library\Data\DataSet;

/**
 * Represents a file produced by the mysqldump command of a single table.
 * 
 * @author Tom Gray
 * @version 1.0 Thursday 3rd December 2015
 * @copyright 2015 Digital Grease Limited.
 */
class MySqlTableDumpFile
{
    /**
     * The field names of the table.
     * 
     * @var array
     */
    protected $fieldNames;
    
    /**
     * The full path to the file.
     * 
     * @var string $filePath
     */
    protected $filePath;
    
    /**
     * Construct a MySQL table dump file.
     * 
     * @param string $filePath Full path to the dump file to open and read in
     *  the data from.
     */
    public function __construct(
        $filePath = null
    ) {
        $this->fieldNames = [];
        $this->filePath = $filePath;
    }
    
    /**
     * Read in the table data from the file.
     * 
     * @return DataSet 
     * 
     * @throws \Exception if the file path does not exist or the file cannot be
     *  opened.
     */
    public function readFromFile($filePath = null)
    {
        if (!$filePath) {
            $filePath = $this->filePath;
        }
        
        if ($filePath) {
            if (is_file($filePath)) {
                $fileContents = file_get_contents($filePath);

                if ($fileContents) {
                    $data = [];
                    $lines = explode(PHP_EOL, $fileContents);

                    // Extract the field names.
                    $iLine = 0;
                    $nLines = count($lines);
                    $foundTableDef = false;
                    while (!$foundTableDef && $iLine < $nLines) {
                        $foundTableDef = substr($lines[$iLine++], 0, 12) == 'CREATE TABLE';
                    }
                    if ($foundTableDef) {
                        $isFieldName = true;
                        while ($isFieldName) {
                            $line = trim($lines[$iLine++]);
                            $this->fieldNames[] = substr($line, 1, strrpos($line, '`') - 1);
                            $isFieldName = $line = trim($lines[$iLine])[0] == '`';
                        }
                    }
                    
                    // Extract the data.
                    // Find the insert statement.
                    $foundInsert = false;
                    while (!$foundInsert && $iLine < $nLines) {
                        ++$iLine;
                        $foundInsert = substr($lines[$iLine], 0, 11) == 'INSERT INTO';
                    }
                    
                    if ($foundInsert) {
                        $line = $lines[$iLine];
                        $iChar = 23;
                        
                        // Continue to the end of the data.
                        while ($line[$iChar] != ';') {
//                            echo $line[$iChar].' => NOT AT END ;'.PHP_EOL;
                            
                            // Move to start of a set of values.
                            while ($line[$iChar++] != '(') {
//                                echo $line[$iChar].' => NOT AT START OF VALUES ('.PHP_EOL;
                            }
//                            echo $line[$iChar].' => FOUND START ('.PHP_EOL;
                            
                            // Add data until the end of the set of values are
                            // reached.
                            $row = [];
                            $iField = 0;
                            while ($line[$iChar] != ')') {
//                                echo $line[$iChar].' => NOT AT END OF VALUES )'.PHP_EOL;
                                $value = '';
                                
                                // If data is a string enclosed in the text delimiter then find
                                // the matching end delimiter, not a delimiter escaped with a slash or a field delimiter.
                                if ($line[$iChar] == '\'') {
//                                    echo $line[$iChar].' => TEXT DELIMITER FOUND'.PHP_EOL;
                                    $iChar++;
                                    while ($line[$iChar] != '\'' || ($line[$iChar - 1] == '\\')) {
//                                        echo $line[$iChar].' => NOT TEXT DELIMITER OR ESCAPED'.PHP_EOL;
                                        $value .= $line[$iChar];
//                                        echo $value.PHP_EOL;
                                        ++$iChar;
                                    }
//                                    echo $line[$iChar].' => END TEXT DELIMITER FOUND'.PHP_EOL;
                                    ++$iChar;
                                } else {
//                                    echo $line[$iChar].' => NO TEXT DELIMITER'.PHP_EOL;
                                }
                                
                                // If data not enclosed in the text delimiter then find
                                // the data before the field delimiter.
                                while (
                                    $line[$iChar] != ',' && $line[$iChar] != ')'
                                ) {
//                                    echo $line[$iChar].' => NOT FIELD DELIMITER OR END OF VALUES'.PHP_EOL;
                                    $value .= $line[$iChar++];
//                                    echo $value.PHP_EOL;
                                }
                                
//                                echo $line[$iChar].' => FIELD DELIMITER OR END OF VALUES FOUND'.PHP_EOL;
//                                echo $value.PHP_EOL;
                                
                                $row[$this->fieldNames[$iField++]] = $value;
                                if ($line[$iChar] == ',') {
                                    ++$iChar;
                                }
                            }
                            ++$iChar;
                            $data[] = $row;
//                            var_dump($data);die;
                        }
//                        var_dump($data);die;
                    }
                } else {
                    throw new \Exception('Dump file cannot be opened or is empty');
                }
            } else {
                throw new \Exception('File does not exist');
            }
        } else {
            throw new \Exception(
                'No file path defined to read in mysqldump file'
            );
        }
        
        return new DataSet($data);
    }
}

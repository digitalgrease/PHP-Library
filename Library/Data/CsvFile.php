<?php

// DO TG Feature: Parse headings and generate the headings list in the order
//  that they appear in each set of data. Generate a warning if the heading
//  order varies between sets of data or one heading does not appear at the
//  start of each set of data as this is required to know when one row ends and
//  a new row is to be generated. (See transform()).

// DO TG Refactor: Rewrite the class to use a dataset and remove all the
//  duplicated functions.

/*
 * Copyright (c) 2013-2015 Greasy Lab.
 */

namespace GreasyLab\Library\Data;

require_once 'DataSet.php';

use GreasyLab\Library\DataSet;

/**
 * Represent, manipulate and create a file of comma-separated values.
 * RFC4180 specifies a suggested standard and guidelines for CSV files that this
 * class follows.
 * 
 * @author Tom Gray
 * @copyright 2013-2015 Greasy Lab
 */
class CsvFile
{
    /**
     * The file content. This is an array of arrays where the outer array holds
     * the rows in the file and each inner array holds the column values for
     * the row.
     * 
     * @var array $data
     */
    protected $data;
    
    /**
     * The field names for this CSV.
     * 
     * @var array
     */
    protected $fieldNames;
    
    /**
     * The type of headings that the CSV file contains.
     * 
     * 0 = none
     * 1 = first row contains headings
     * 2 = first column contains a field name where each row of the CSV consists
     *  of key value pairs
     * 
     * @var int
     */
    protected $headings;
    
    /**
     * The full path to the file.
     * 
     * @var string $_filePath
     */
    protected $_filePath;
    
    /**
     * The character used to divide each field and define the limits of the
     * data.
     * 
     * @var char $_fieldDelimiter
     */
    protected $_fieldDelimiter;
    
    /**
     * The character used to enclose the data of each field when the data
     * contains the field delimiter character.
     * 
     * @var char $_textDelimiter
     */
    protected $_textDelimiter;
    
    /**
     * Construct a CSV file.
     * 
     * @param string $filePath Full path to the CSV file to open and read in the
     *  data from.
     * @param int $headings The type of headings that the CSV file contains.
     * @param char $fieldDelimiter Character that separates the column data
     *  values.
     * @param char $textDelimiter Character that encloses the column data
     *  values.
     */
    public function __construct(
        array $data,
        $filePath = null,
        $headings = 0,
        $fieldDelimiter = ',',
        $textDelimiter = '"'
    ) {
        $this->data = $data;
        $this->_filePath = $filePath;
        $this->_fieldDelimiter = $fieldDelimiter;
        $this->_textDelimiter = $textDelimiter;
        $this->headings = $headings;
        $this->fieldNames = [];
    }
    
    /**
     * Get the total number of rows of data in the file.
     * 
     * @return integer The total number of rows.
     */
    public function getNumberOfRows()
    {
        return count($this->data);
    }
    
    /**
     * Get the total number of columns of data in the file.
     * 
     * @return integer The total number of columns.
     */
    public function getNumberOfColumns()
    {
        return count( $this->data[0] );
    }
    
    /**
     * Get the data values of the given row.
     * 
     * @param integer $iRow Row number to return the data of.
     * 
     * @return array The row values.
     */
    public function getRowDataAsArray( $iRow )
    {
        return $this->data[$iRow];
    }
    
    /**
     * Get the data values of the given column.
     * 
     * @param integer $iColumn Column number to return to data of.
     * 
     * @return array The column values.
     */
    public function getColumnDataAsArray( $iColumn )
    {
        $columnData = array();
        foreach ($this->data as $row) {
            $columnData[] = $row[$iColumn];
        }
        return $columnData;
    }
    
    /**
     * Get all the data.
     * 
     * @return array $data An array of arrays containing all the data from the
     *                      file.
     */
    public function getData()
    {
        return $this->data;
    }
    
    /**
     * TODO !Comment
     * 
     * @param unknown_type $iRow
     * @param unknown_type $iColumn
     */
    public function getValue( $iRow, $iColumn )
    {
        return $this->data[$iRow][$iColumn];
    }
    
    /**
     * Delete the given row of data and move all the rows below up one row. Row
     * numbers are specified starting from 0.
     * 
     * @param integer $iRow Row number to delete.
     */
    public function deleteRow( $iRow )
    {
        $nRows = $this->getNumberOfRows();
        if ($iRow > -1 && $iRow < $nRows ) {
            for ($iRow; $iRow < $nRows - 1; $iRow++) {
                $this->data[$iRow] = $this->data[$iRow + 1];
            }
            unset( $this->data[$nRows - 1] );
        }
    }
    
    /**
     * Delete the list of rows from the CSV file.
     * 
     * @param array $rows The numbers of the rows to delete.
     */
    public function deleteRows( $rows )
    {
        rsort( $rows );
        foreach ($rows as $row) {
            $this->deleteRow( $row );
        }
    }
    
    /**
     * Delete the given column of data and move all the columns on the right
     * left one column. Column numbers are specified starting from 0.
     * 
     * @param integer $iColumn Column number to delete.
     */
    public function deleteColumn( $iColumn )
    {
        $nColumns = $this->getNumberOfColumns();
        if ($iColumn > -1 && $iColumn < $nColumns) {
            foreach ($this->data as $iRow => $rowData) {
                for ($iCol = $iColumn; $iCol < $nColumns - 1; $iCol++) {
                    $this->data[$iRow][$iCol] =
                            $this->data[$iRow][$iCol + 1];
                }
                unset( $this->data[$iRow][$nColumns - 1] );
            }
        }
    }
    
    /**
     * Delete the list of columns from the CSV file.
     * 
     * @param array $columns The numbers of the columns to delete.
     */
    public function deleteColumns( $columns )
    {
        rsort( $columns );
        foreach ($columns as $column) {
            $this->deleteColumn( $column );
        }
    }
    
    /**
     * Get an array of all the different column values.
     * 
     * @param integer $iColumn Column number to inspect for the values.
     * 
     * @return array List of the different column values.
     */
    public function getDiscreteColumnValues( $iColumn )
    {
        $values = array();
        foreach ($this->data as $row) {
            if (!in_array( $row[$iColumn], $values )) {
                $values[] = $row[$iColumn];
            }
        }
        return $values;
    }
    
    /**
     * Get the number of occurrences of the given value in the given column.
     * 
     * @param integer $iColumn The column number to check for occurences of the
     *                         value.
     * @param mixed   $value   The value to look for occurences of.
     * 
     * @return integer The number of occurences of the value in the column.
     */
    public function getValueCount( $iColumn, $value )
    {
        $count = 0;
        foreach ($this->data as $row) {
            if ($row[$iColumn] === $value) {
                $count++;
            }
        }
        return $count;
    }
    
    /**
     * TODO !Comment
     */
    // FURTHER DEVELOPMENT CsvFile::insertValue()
    // Validate the given row and column indexes.
    public function insertValue( $value, $iRow, $iColumn )
    {
        $this->data[$iRow][$iColumn] = $value;
    }
    
    /**
     * Read in data from file.
     * 
     * @return DataSet 
     * 
     * @throws \Exception if the file path does not exist or the file cannot be
     *  opened.
     */
    public function readFromFile($filePath = null)
    {
        // Read in the file data.
        if (!$filePath) {
            $filePath = $this->_filePath;
        }
        
        if ($filePath) {
            // DO TG Validate the file exists - i.e. MySqlDumpFile
            $fileContents = file_get_contents($filePath);
            
            if ($fileContents) {
                $this->data = [];
                
                $lines = explode(PHP_EOL, $fileContents);
                
                $this->setFieldNames($lines);
                // Skip the first line if they are headings.
                if (1 == $this->headings) {
                    $i = 1;
                } else {
                    $i = 0;
                }
                
                for (; $i < count($lines); ++$i) {
                    $this->data[] = $this->parseLine($lines[$i]);
                }
                
                // If the last row is an empty line then remove it.
                $nRows = count($this->data);
                if (count($this->data[$nRows - 1]) < count($this->data[0])) {
                    unset($this->data[$nRows - 1]);
                }
            }
            else {
                throw new \Exception(
                    'CsvFile::__construct() - CSV File could not be opened.'
                );
            }
        }
        return new DataSet($this->data);
    }

    /**
     * Replace all occurences of a value with another value in the given
     * column.
     * 
     * @param integer $iColumn  Number of the column within which to replace
     *                          the values.
     * @param mixed   $oldValue The value to be replaced.
     * @param mixed   $newValue The value to be inserted.
     */
    public function replaceValues( $iColumn, $oldValue, $newValue )
    {
        foreach ($this->data as $iRow => $rowData) {
            if ($this->data[$iRow][$iColumn] === $oldValue) {
                $this->data[$iRow][$iColumn] = $newValue;
            }
        }
    }
    
    /**
     * Prepend all values or just occurences of a specified value in the given
     * column with the given prefix.
     * 
     * @param integer $iColumn The column number of the column containing the
     *                         values to prepend.
     * @param string  $prefix  The prefix to be prepended to the value.
     * @param mixed   $value   The value to attach the prefix to.
     */
    public function prependValues( $iColumn, $prefix, $value = null )
    {
        if (null == $value) {
            foreach ($this->data as $iRow => $rowData) {
                $this->data[$iRow][$iColumn] =
                        $prefix . $this->data[$iRow][$iColumn];
            }
        }
        else {
            foreach ($this->data as $iRow => $rowData) {
                if ($this->data[$iRow][$iColumn] === $value ) {
                    $this->data[$iRow][$iColumn] = $prefix . $value;
                }
            }
        }
    }
    
    /**
     * Append all or just occurences of a specified value in the given column
     * with the given suffix.
     * 
     * @param integer $iColumn The column number of the column containing the
     *                         values to append.
     * @param string  $suffix  The suffix to be appended to the value.
     * @param mixed   $value   The value to attach the suffix to.
     */
    public function appendValues( $iColumn, $suffix, $value = null )
    {
        if (null == $value) {
            foreach ($this->data as $iRow => $rowData) {
                $this->data[$iRow][$iColumn] .= $suffix;
            }
        }
        else {
            foreach ($this->data as $iRow => $rowData) {
                if ($this->data[$iRow][$iColumn] === $value ) {
                    $this->data[$iRow][$iColumn] .= $suffix;
                }
            }
        }
    }
    
    /**
     * Convert string dates in the data to DateTime objects for use with
     * doctrine or further manipulation.
     * 
     * @param integer $iColumn The number of the column containing the dates to
     *                         be converted.
     * @param string  $format  Format of the date to be converted. The format
     *                         string used by DateTime::createFromFormat().
     */
    public function convertValuesToDateTime( $iColumn, $format )
    {
        $nRows = $this->getNumberOfRows();
        for ($iRow = 1; $iRow < $nRows; $iRow++) {
            $this->data[$iRow][$iColumn] =
                    DateTime::createFromFormat(
                            $format,
                            $this->data[$iRow][$iColumn]
                    );
        }
    }
    
    /**
     * Write the data out to a file. If the file path, field delimiter or text
     * delimiter are not specified then the object's default values will be
     * used. If specific columns and rows are not given then all columns and
     * rows will be included in the output.
     * 
     * @param string $filePath       Full file path to write the data to.
     * @param string $fieldDelimiter The field delimiter to use in the output.
     * @param string $textDelimiter  The text delimiter to use in the output.
     * @param array  $columns        Only include the specific column numbers
     *                               given in the output.
     * @param array  $rows           Only include the specific row numbers to
     *                               given in the output.
     * 
     * @return integer The number of bytes that were written to the file, or
     *                 false on failure.
     */
    // FURTHER DEVELOPMENT CsvFile::toFile()
    // The use of a custom/user defined text delimiter is not implemented.
    public function toFile(
        $filePath = null,
        $columns = null,
        $rows = null,
        $fieldDelimiter = ',',
        $textDelimiter = '"'
    ) {
        
        // Set the file path.
        if (null == $filePath) {
            $filePath = $this->_filePath;
            if (null == $filePath) {
                throw new Exception("CsvFile::toFile() - No file path has been"
                        . " specified and the default value is not set.");
            }
        }
        
        // If specific rows are not specified then include all rows in the data
        // set.
        if (null == $rows) {
            $rows = array();
            $nRows = $this->getNumberOfRows();
            for ($iRow = 0; $iRow < $nRows; $iRow++) {
                $rows[] = $iRow;
            }
        }
        
        // If specific columns are not specified then include all columns in
        // the data set.
        if (null == $columns) {
            $columns = array();
            $nCols = $this->getNumberOfColumns();
            for ($iCol = 0; $iCol < $nCols; $iCol++) {
                $columns[] = $iCol;
            }
        }
        
        // Build the data to write to file.
        $data = "";
        foreach ($rows as $row) {
            foreach ($columns as $column) {
                $data .= $textDelimiter . $this->data[$row][$column] . $textDelimiter . $fieldDelimiter;
            }
            
            // Remove the last field delimiter from the end of the row.
            $data = substr($data, 0, -1);
            $data .= PHP_EOL;
        }
        
        // Remove the last newline character from the end of the last row.
        $data = substr( $data, 0, -1 );
        
        // Write the data to file.
        return file_put_contents( $filePath, $data );
    }
    
    /**
     * Join another file of comma-separated values to this one. The given file
     * to be joined must contain the same number of rows as this file being
     * joined to.
     * 
     * @param CsvFile $csvFile The CSV file to be joined to this file.
     * 
     * @return boolean True if the given file is successfully joined to this
     *                 file, false otherwise.
     */
    public function join(CsvFile $csvFile)
    {
        $isJoined = false;
        if ($this->getNumberOfRows() == $csvFile->getNumberOfRows()) {
            $nColumns = $this->getNumberOfColumns();
            foreach ($this->data as $iRow => $rowData) {
                foreach ($csvFile->getRowDataAsArray( $iRow )
                        as $iColumn => $columnData
                ) {
                    $this->data[$iRow][$nColumns + $iColumn] = $columnData;
                }
            }
            $isJoined = true;
        }
        return $isJoined;
    }
    
    /**
     * Add a new column of default values.
     * 
     * @param string $heading Heading for the column of values.
     * @param mixed  $value   Default value(s) to populate the rows with.
     */
    public function addColumn( $heading = null, $value = null )
    {
        $iColumn = $this->getNumberOfColumns();
        
        // Set the default values.
        if (is_array( $value )) {
            foreach ($this->data as $iRow => $rowData) {
                $this->data[$iRow][$iColumn] = $value[$iRow];
            }
        }
        else {
            if (null == $value) {
                $value = "";
            }
            foreach ($this->data as $iRow => $rowData) {
                $this->data[$iRow][$iColumn] = $value;
            }
        }
        
        // Set the heading.
        $this->data[0][$iColumn] = (null != $heading)
                ? $heading
                : $this->data[0][$iColumn];
    }
    
    /**
     * Add a new row of data.
     * 
     * @param array $data
     */
    public function addRow(array $data)
    {
        $iRow = $this->getNumberOfRows();
        $this->data[$iRow] = $data;
    }
    
    /*
     * FURTHER DEVELOPMENT CsvFile::getDataAsDoctrineValues()
     * Return the CSV values as an array of associated
     * arrays, where the keys of the inner arrays are the column headings. The
     * output values can then be passed to a doctrine model and the values
     * added to a corresponding database table.
     *//*
    public function getDataAsDoctrineValues( $startRow, $endRow )
    {
        $records = array();
        $nCols = $this->getNumberOfColumns();
        
        // Check and set the starting row.
        if ($startRow < 1) {
            $startRow = 1;
        }
        else if ($startRow >= $this->getNumberOfRows()) {
            $startRow = $this->getNumberOfRows() - 1;
        }
        
        // Check and set the end row.
        if ($endRow < 1) {
            $endRow = 1;
        }
        else if ($endRow >= $this->getNumberOfRows()) {
            $endRow = $this->getNumberOfRows() - 1;
        }
        
        // Get the data.
        for ($iRow = $startRow; $iRow <= $endRow; $iRow++) {
            $records[$iRow] = array();
            for ($iCol = 0; $iCol < $nCols; $iCol++) {
                if (!empty( $this->data[$iRow][$iCol] )) {
                    $records[$iRow][$this->data[0][$iCol]] =
                            $this->data[$iRow][$iCol];
                }
                else {
                    unset( $records[$iRow][$this->data[0][$iCol]] );
                }
            }
        }
        return $records;
    }*/
    
    /**
     * FURTHER DEVELOPMENT CsvFile::writeToDatabase()
     */
    public function writeToDatabase(
            $tableName,
            $host = null,
            $username = null,
            $password = null,
            $databaseName = null,
            $port = null,
            $socket = null
    ) {
        
        // Setup the database connection.
        $mysqli = new mysqli(
                $host,
                $username,
                $password,
                $databaseName,
                $port,
                $socket
        );
        
        // Build the start of the insert query and set the field headings.
        $insert = "INSERT INTO $tableName (";
        foreach ($this->data[0] as $heading) {
            $insert .= $heading . ", ";
        }
        $insert = substr( $insert, 0, -2 );
        $insert .= ") VALUES ";
        
        // Loop over each row.
        $nRows = $this->getNumberOfRows();
        $valuesList = "";
        $iRow = 1;
        $hasWritten = true;
        while ($iRow < $nRows && $hasWritten) {
            
            // Build the values for the row.
            $values = "(";
            foreach ($this->data[$iRow] as $column) {
                $values .= "'" . $mysqli->real_escape_string( $column ) . "',";
            }
            $values = substr( $values, 0, -1 );
            $values .= ")";
            
            // Add the row values to the list for the query.
            $valuesList .= $values;
            
            // Perform the query if the list contains 100 rows or the last row
            // has been reached.
            if (0 == $iRow % 100 || $nRows - 1 == $iRow) {
                $hasWritten = $mysqli->query( $insert . $valuesList );
                $valuesList = "";
            }
            
            // Otherwise add a comma ready for the next set of values.
            else {
                $valuesList .= ", ";
            }
            $iRow++;
        }
        
        return $hasWritten;
    }
    
    /**
     * Transform the data in the file.
     * Written specifically for the physio data retrieved from the web.
     * FURTHER DEVELOPMENT:
     *  - Add flag for having column headings.
     *  - Add flag for having row headings.
     *  - Add method to transpose the data between row headings and column headings.
     *  - Add a method to remove duplicate headings and put the data all under single headings.
     */
    public function transform()
    {
        // Retrieve and set the column headings.
        $headings = $this->getDiscreteColumnValues(0);
        
        // One of the retrieved headings does not appear in the first set of data and so is added to the end of the
        // headings. When parsing the data though it appears as the penultimate heading, and so the order of headings
        // needs to be adjusted so that it can be known when a set of data has been parsed and a new set is begining.
        // FURTHER DEVELOPMENT: Parse headings and generate the headings list in the order that they appear in each set
        // of data. Generate a warning if the heading order varies between sets of data or one heading does not appear
        // at the start of each set of data as this is required to know when one row ends and a new row is to be
        // generated.
        $headings[6] = $headings[4];
        $headings[4] = $headings[5];
        $headings[5] = $headings[6];
        
        // Add additional headings we want.
        $headings[6] = 'Title';
        $headings[7] = 'First Name';
        $headings[8] = 'Surname';
        
        // Create the new output CSV.
        $outputFile = new CsvFile();
        $outputFile->addRow($headings);
        
        // Set up a new empty row to fill with data.
        $emptyRow = [];
        for ($i = 0; $i < count($headings); ++$i) {
            $emptyRow[$i] = '';
        }
        
        // Loop over the data and build the rows.
        $row = $emptyRow;
        $lastIndex = 0;
        foreach ($this->data as $iRow => $data) {
            $heading = $data[0];
            $value = $data[1];
            
            $index = array_search($heading, $headings);
            
            // If the row is complete, add it to the output and start a new row.
            if ($index < $lastIndex) {
                $outputFile->addRow($row);
                $row = $emptyRow;
            }
            
            // Add the data to the row.
            $row[$index] = $value;
            $lastIndex = $index;
            
            // Split any name into the 3 parts.
            // FURTHER DEVELOPMENT: Use a library util function for this.
            if ($index === 0) {
                
                $titles = [
                    'Mr',
                    'Mrs',
                    'Miss',
                    'Ms',
                    'Dr',
                    'Major',
                    'Lt Col'
                ];
                
                // Find any title of the name.
                if (in_array(substr($value, 0, 4), $titles)) {
                    $row[6] = substr($value, 0, 4);
                    $value = substr($value, 5);
                } elseif (in_array(substr($value, 0, 3), $titles)) {
                    $row[6] = substr($value, 0, 3);
                    $value = substr($value, 4);
                } elseif (in_array(substr($value, 0, 2), $titles)) {
                    $row[6] = substr($value, 0, 2);
                    $value = substr($value, 3);
                }
                
                // Extract first name.
                $row[7] = substr($value, 0, strpos($value, ' '));
                
                // Extract surname;
                $row[8] = substr($value, strpos($value, ' ') + 1);
            }
        }
        
        // Add the last completed row.
        $outputFile->addRow($row);
        
        $outputFile->toFile('/home/tom/Desktop/new-physio-data.csv');
    }
    
    /**
     * Set the field names from any headings in the CSV.
     * 
     * @param array $lines
     * 
     * @return void
     */
    protected function setFieldNames(array $lines)
    {
        switch ($this->headings) {
            
            // No headings, field names are integer indexes.
            case 0:
                $row = $this->parseLine($lines[0]);
                for ($i = 0; $i < count($row); ++$i) {
                    $this->fieldNames[$i] = $i;
                }
                break;
                
            // First row contains headings.
            case 1:
                $this->fieldNames = $this->parseLine($lines[0]);
                break;
                
            case 2:
                throw new \Exception('Not implemented');
        }
    }
    
    /**
     * 
     * @param string $line
     * 
     * @return array Row of data.
     */
    protected function parseLine($line)
    {
        $row = [];
        $chars = str_split(trim($line));
        $iChar = 0;
        $nChars = count($chars);
        $iCol = 0;
        $fieldName = isset($this->fieldNames[$iCol])
            ? $this->fieldNames[$iCol]
            : $iCol;
        
        while ($iChar < $nChars) {
            $columnValue = array();

            // If data is enclosed in the text delimiter then find
            // the matching end delimiter, not a delimiter escaped
            // by itself within delimiters.
            while (
                $iChar < $nChars
                && $chars[$iChar] == $this->_textDelimiter
            ) {
                $columnValue[] = $chars[$iChar];
                $iChar++;
                while (
                    $iChar < $nChars
                    && $chars[$iChar] != $this->_textDelimiter
                ) {
                    $columnValue[] = $chars[$iChar];
                    $iChar++;
                }
                $columnValue[] = $chars[$iChar];
                $iChar++;
            }

            // If data not enclosed in the text delimiter then find
            // the data before the field delimiter.
            while (
                $iChar < $nChars
                && $chars[$iChar] != $this->_fieldDelimiter
            ) {
                $columnValue[] = $chars[$iChar];
                $iChar++;
            }

            // Store the complete column value and start searching
            // for the next.
            $row[$fieldName] = implode($columnValue);
            $iCol++;
            $fieldName = isset($this->fieldNames[$iCol])
                ? $this->fieldNames[$iCol]
                : $iCol;
            $iChar++;
        }

        // Check the last character and if it is a field delimiter
        // then there is an additional blank column value at the
        // end of the record.
        if ($chars[$nChars - 1] == $this->_fieldDelimiter) {
            $row[$fieldName] = "";
        }
        
        return $row;
    }
}

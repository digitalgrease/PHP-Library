<?php

// DO TG Feature: Allow simple or more advanced criteria to be used
//  interchangeably.
// DO TG Feature: Allow the class to be iterated over.
// DO TG Feature: Schema compare/diff.
// DO TG Feature: Data compare/diff for a table row/table/etc.

/*
 * Copyright (c) 2015 Digital Grease Limited.
 * 
 * Tom Gray
 * 
 * Date: 20th September 2015
 */

namespace DigitalGrease\Library\Data;

/**
 * Represent and manipulate data sets.
 * 
 * @version 1.0 20th September 2015
 * @author Tom Gray
 * @copyright 2015 Digital Grease Limited
 */
class DataSet
{
    /**
     * The matrix of data that makes up the dataset.
     * 
     * @var array $data
     */
    protected $data;
    
    /**
     * Create a DataSet from a CSV file.
     *
     * @param string $filename
     *
     * @return DataSet
     */
    public static function fromCsv(string $filename): DataSet
    {
        return new DataSet((new CsvFile())->readFromFile($filename)->getData());
    }
    
//    /**
//     * The name of the field or the number of the column or row that is the
//     * primary key?
//     * 
//     * @var mixed
//     */
//    protected $keys;
//    
//    protected $form;
//        matrix
//        column headings and rows as records
//        row headings and columns as records
//        records as key value pairs in rows
//        records as key value pairs in columns
//    
//    protected $fields;
//        name
//        data type:
//            string
//            integer
//            float
//            date/time
//        value type:
//            fluctuate       - average, lowest, highest, variance, median, mode
//            accumulative    - rate over time
//            key             - n/a
    
    /**
     * Construct a dataset.
     * 
     * @param array Matrix of data that makes up the dataset.
     * 
     * @throws \InvalidArgumentException
     */
    public function __construct(array $data = [])
    {
        $fields = [];
        $iRow = 0;
        
        foreach ($data as $row) {
            
            // Check each row has the same number of fields as the first row.
            if ($iRow > 0 && count($row) > count($fields)) {
                throw new \InvalidArgumentException(
                    'Row index '.$iRow.' has more fields than the first row.'
                    .PHP_EOL.'First row: '
                    .print_r($data[0], true)
                    .'Row '.$iRow.': '
                    .print_r($data[$iRow], true)
                );
            } elseif ($iRow > 0 && count($row) < count($fields)) {
                
                // DO TG: If there is a newline character in the column this can occur. Check the next line for the missing columns?
                // Example is 81renshaw/res/discogs-exports/81Renshaw-inventory-20220401-0331.csv.zip
                
                throw new \InvalidArgumentException(
                    'Row index '.$iRow.' has less fields than the first row.'
                    .PHP_EOL.'First row: '
                    .print_r($data[0], true)
                    .'Row '.$iRow.': '
                    .print_r($data[$iRow], true)
                );
            }
            
            // Build a list of fields from the first row and check each row has
            // consistent fields/indexes.
            foreach ($row as $field => $value) {
                if (0 == $iRow) {
                    $fields[] = $field;
                } elseif (!in_array($field, $fields)) {
                    throw new \InvalidArgumentException(
                        'Row index '.$iRow.' has a field "'.$field.'" that does'
                        . ' not appear in the first row.'
                        .PHP_EOL.'First row: '
                        .print_r($data[0], true)
                        .'Row '.$iRow.': '
                        .print_r($data[$iRow], true)
                    );
                }
            }
            ++$iRow;
        }
        
        $this->data = $data;
    }
    
    /**
     * Get the total number of rows of data in the file.
     * 
     * @return int The total number of rows.
     */
    public function getNumberOfRows()
    {
        return count($this->data);
    }
    
    /**
     * Get the total number of columns of data in the file.
     * 
     * @return int The total number of columns.
     */
    public function getNumberOfColumns()
    {
        if (count($this->data) > 0) {
            return count($this->data[0]);
        }
        return 0;
    }
    
    /**
     * Get the data values of the given row.
     * 
     * @param int $row The row index.
     * 
     * @return array The row values.
     * 
     * @throws \InvalidArgumentException|\Exception
     */
    public function getRow($row = 0)
    {
        if ($this->isValidRow($row)) {
            return $this->data[$row];
        }
    }
    
    /**
     * Get the data values of the given column.
     * 
     * @param int|string $column The column index or field name.
     * 
     * @return array The column values.
     * 
     * @throws \InvalidArgumentException|\Exception
     */
    public function getColumn($column)
    {
        if ($this->isValidColumn($column)) {
            $columnData = array();
            foreach ($this->data as $row) {
                $columnData[] = $row[$column];
            }
            return $columnData;
        }
    }
    
    /**
     * Get the matrix of data that makes up the dataset.
     * 
     * @return array $data
     * @deprecated since version 1.0 Allow the class to be iterated over to
     *  access the data.
     */
    public function getData()
    {
        return $this->data;
    }
    
    /**
     * Get the rows of data that make up the dataset.
     * 
     * @return array
     */
    public function getRows()
    {
        return $this->data;
    }
    
    /**
     * DO TG DataSet: API Improvement: Swap args and set $row as optional with
     * default of 0.
     * 
     * Get a value from the dataset.
     * 
     * @param int $row The row index.
     * @param int|string $column The column index or field name.
     * 
     * @return mixed
     * 
     * @throws \InvalidArgumentException|\Exception
     */
    public function getValue($row, $column)
    {
        if ($this->isValidRow($row) && $this->isValidColumn($column)) {
            return $this->data[$row][$column];
        }
    }
    
    /**
     * Delete the given row of data and move all the rows below up one row.
     * Row numbers are specified starting from 0.
     * 
     * @param int $row Row index to delete.
     * 
     * @return DataSet This dataset to allow method chaining.
     * 
     * @throws \InvalidArgumentException|\Exception
     */
    public function deleteRow($row)
    {
        if ($this->isValidRow($row)) {
            $nRows = count($this->data);
            for ($row; $row < $nRows - 1; $row++) {
                $this->data[$row] = $this->data[$row + 1];
            }
            unset($this->data[$nRows - 1]);
        }
        return $this;
    }
    
    /**
     * Delete a set of rows from the dataset.
     * 
     * @param array $rows The indexes of the rows to delete.
     * 
     * @return DataSet This dataset to allow method chaining.
     * 
     * @throws \InvalidArgumentException|\Exception
     */
    public function deleteRows(array $rows)
    {
        // Deleting the rows from high to low keeps the indexes consistent,
        // otherwise the indexes would change between deletes and possibly be
        // removed.
        rsort($rows);
        foreach ($rows as $row) {
            $this->deleteRow($row);
        }
        return $this;
    }
    
    /**
     * Delete a column of data.
     * 
     * @param int|string $column Column to delete.
     * 
     * @return DataSet This dataset to allow method chaining.
     * 
     * @throws \InvalidArgumentException|\Exception
     */
    public function deleteColumn($column)
    {
        if ($this->isValidColumn($column)) {
            $isIndex = is_int($column);
            foreach ($this->data as &$row) {
                unset($row[$column]);
                if ($isIndex) {
                    $row = array_values($row);
                }
            }
        }
        return $this;
    }
    
    /**
     * Delete a list of columns.
     * 
     * @param array $columns The columns to delete.
     * 
     * @return DataSet This dataset to allow method chaining.
     * 
     * @throws \InvalidArgumentException|\Exception
     */
    public function deleteColumns(array $columns)
    {
        rsort($columns);
        foreach ($columns as $column) {
            $this->deleteColumn($column);
        }
        return $this;
    }
    
    /**
     * Get an array of all the different column values.
     * 
     * @param int|string $column Column to inspect for the values.
     * 
     * @return array List of the different column values.
     * 
     * @throws \InvalidArgumentException|\Exception
     */
    public function getDistinctColumnValues($column)
    {
        $values = array();
        if ($this->isValidColumn($column)) {
            foreach ($this->data as $row) {
                if (!in_array($row[$column], $values)) {
                    $values[] = $row[$column];
                }
            }
        }
        return $values;
    }
    
    /**
     * Get the number of occurrences of a value in a column.
     * 
     * @param int|string $column The column to look in.
     * @param mixed      $value  The value to look for occurrences of.
     * 
     * @return int The number of occurences of the value in the column.
     * 
     * @throws \InvalidArgumentException|\Exception
     */
    public function getValueCount($column, $value)
    {
        $count = 0;
        if ($this->isValidColumn($column)) {
            foreach ($this->data as $row) {
                if ($row[$column] === $value) {
                    ++$count;
                }
            }
        }
        return $count;
    }
    
    /**
     * Insert a value into the dataset at a specific location.
     * 
     * @param int $row
     * @param int|string $column
     * @param mixed $value
     * 
     * @return DataSet This dataset to allow method chaining.
     * 
     * @throws \InvalidArgumentException|\Exception
     */
    public function insertValue($row, $column, $value)
    {
        if ($this->isValidRow($row) && $this->isValidColumn($column)) {
            $this->data[$row][$column] = $value;
        }
        return $this;
    }
    
    /**
     * Get whether this dataset is empty.
     * 
     * @return boolean
     */
    public function isEmpty()
    {
        return count($this->data) == 0;
    }
    
    /**
     * Replace all occurences of a value with another value throughout the
     * entire data set.
     * 
     * @param string $oldValue
     * @param string $newValue
     * 
     * @return DataSet This dataset to allow method chaining.
     * 
     * @throws \InvalidArgumentException|\Exception
     */
    public function replaceAllValues($oldValue, $newValue)
    {
        if (!$this->isEmpty()) {
            foreach (array_keys($this->data[0]) as $column) {
                $this->replaceValues($column, $oldValue, $newValue);
            }
        }
        return $this;
    }
    
    /**
     * Replace all occurences of a value with another value in a column.
     * 
     * @param int|string $column   Column within which to replace the values.
     * @param mixed      $oldValue The value to be replaced.
     * @param mixed      $newValue The value to be inserted.
     * 
     * @return DataSet This dataset to allow method chaining.
     * 
     * @throws \InvalidArgumentException|\Exception
     */
    public function replaceValues($column, $oldValue, $newValue)
    {
        if ($this->isValidColumn($column)) {
            foreach ($this->data as &$row) {
                if ($row[$column] === $oldValue) {
                    $row[$column] = $newValue;
                }
            }
        }
        return $this;
    }
    
    /**
     * DO TG DataSet Improvement: Validate columns.
     * 
     * Merge a set of columns into one column in the order provided with the
     * "glue" provided.
     * 
     * @param array $columns
     * @param string $glue
     * 
     * @return DataSet This dataset to allow method chaining.
     */
    public function mergeColumns(array $columns, $glue)
    {
        foreach ($this->data as &$row) {
            $isFirstColumn = true;
            foreach ($columns as $column) {
                if ($isFirstColumn) {
                    $isFirstColumn = false;
                    $firstColumn = $column;
                } else {
                    if ($row[$column]) {
                        if ($row[$firstColumn]) {
                            $row[$firstColumn] .= $glue . $row[$column];
                        } else {
                            $row[$firstColumn] .= $row[$column];
                        }
                    }
                    unset($row[$column]);
                }
            }
            // DO TG DataSet IMPORTANT! Bug/Improvement: This line should only be perfomred if the columns are indexed and not string names.
            $row = array_values($row);
        }
        return $this;
    }
    
    /** DO TG DataSet: Complete and create tests.
     * Multiply all the values in a column by a multiplier.
     * 
     * @param int|string $column
     * @param int|float $multiplier
     * 
     * @return DataSet
     * 
     * @throws \InvalidArgumentException|\Exception
     */
    public function multiplyValues($column, $multiplier)
    {
        if ($this->isValidColumn($column)) {
            foreach ($this->data as $index => &$row) {
                $row[$column] *= $multiplier;
            }
        }
        return $this;
    }
    
    /** DO TG DataSet: Complete and create tests.
     * Remove any rows where a column contains a value.
     * 
     * DO TG Feature: Remove rows on multiple criteria.
     * DO TG Improvement: Trim comparisons?
     * DO TG Improvement: Ignore case on comparisons?
     * 
     * @param int|string $column
     * @param mixed $value
     * 
     * @return DataSet This dataset to allow method chaining.
     * 
     * @throws \InvalidArgumentException|\Exception
     */
    public function removeRows($column, $value)
    {
        if ($this->isValidColumn($column)) {
            foreach ($this->data as $index => &$row) {
                if ($row[$column] === $value) {
                    unset($this->data[$index]);
                }
            }
        }
        return $this;
    }
    
    // DO TG DataSet: Add pregreplace.
    // DO TG DataSet: Add pregmatch.
    // DO TG DataSet: Add insertColumn($location)
    
    /**
     * Extract matches from a column into new columns.
     *
     * @param string $pattern
     * @param int $column DO TG DataSet: Allow string heading which is converted to integer column ID.
     * @param int $limit DO TG DataSet: Implement the limit.
     *
     * @return self
     */
    public function regexExtract(string $pattern, int $column, ?int $limit = null): self
    {
        if ($this->isValidColumn($column)) {
            
            // Get the maximum number of matches for a single row.
            $maxMatchCount = 0;
            foreach ($this->data as $row) {
                preg_match_all($pattern, $row[$column], $matches);
                if ($matches) {
                    $matchCount = 0;
                    foreach ($matches as $set) {
                        $matchCount += count($set);
                    }
                    $maxMatchCount = max($maxMatchCount, $matchCount);
                }
            }
            
            // Add the columns.
            for ($i = 0; $i < $maxMatchCount; ++$i) {
                $this->addColumn();
            }
            
            // Add the extracted matches.
            foreach ($this->data as &$row) {
                preg_match_all($pattern, $row[$column], $matches);
                if ($matches) {
                    $iMatch = 1;
                    foreach ($matches as $set) {
                        foreach ($set as $match) {
                            $row[$column + $iMatch++] = $match;
                        }
                    }
                }
            }
        }
        
        return $this;
    }

    /** DO TG DataSet: Complete and create tests.
     * Add a prefix to all values, or just occurrences of a specified value, in
     * a column.
     *
     * @param int|string $column The column containing the values to prepend.
     * @param string     $prefix The prefix to add to the values.
     * @param mixed      $value  The value to attach the prefix to.
     *
     * @return DataSet This dataset to allow method chaining.
     *
     * @throws \InvalidArgumentException|\Exception
     */
    public function prependValues($column, $prefix, $value = null)
    {
        if ($this->isValidColumn($column)) {
            foreach ($this->data as &$row) {
                if (
                    ($value === null || $row[$column] === $value)
                    && $row[$column] !== null
                ) {
                    $row[$column] = $prefix . $row[$column];
                }
            }
        }
        return $this;
    }
    
    /**
     * 
     * @param array $columns
     * 
     * @return void
     */
    public function prioritiseDataLeftToRight(array $columns)
    {
        // DO TG Validate columns.
        $nColumns = count($columns);
        foreach ($this->data as &$row) {
            $i = 0;
            $rowComplete = false;
            while (!$rowComplete && $i < $nColumns - 1) {
                if ($row[$columns[$i]]) {
                    ++$i;
                } else {
                    $next = $i + 1;
                    $noData = true;
                    while ($noData && $next < $nColumns) {
                        if ($row[$columns[$next]]) {
                            $row[$columns[$i]] = $row[$columns[$next]];
                            $row[$columns[$next]] = '';
                            $i = $next;
                            $noData = false;
                        } else {
                            ++$next;
                        }
                    }
                    if ($next = $nColumns) {
                        $rowComplete = true;
                    }
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
        } else {
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
     * Add a new column of default values.
     * DO TG Headings on top row?
     * DO TG Actual 'NULL' value?
     * 
     * @param string $heading Heading for the column of values.
     * @param mixed  $value   Default value(s) to populate the rows with.
     */
    public function addColumn($heading = null, $value = null)
    {
        if (!$heading) {
            $heading = $this->getNumberOfColumns();
        }
        
        // Set the default values.
        if (is_array($value)) {
            foreach ($this->data as $iRow => $rowData) {
                $this->data[$iRow][$heading] = $value[$iRow];
            }
        } else {
            if (null == $value) {
                $value = "";
            }
            foreach ($this->data as $iRow => $rowData) {
                $this->data[$iRow][$heading] = $value;
            }
        }
        
//        // Set the heading.
//        $this->data[0][$heading] = (null != $heading)
//                ? $heading
//                : $this->data[0][$heading];
    }
    
    /**
     * DO TG Complete to work with keys and indexes! Only made to work with
     *  indexes for use case when created.
     * 
     * Crop a set of columns.
     * 
     * @param ??? $left
     * @param ??? $right
     * 
     * @return ???
     */
    public function cropColumns($left, $right)
    {
        
        // Delete the columns on the left.
        for ($i = 0; $i < $left; ++$i) {
            $this->deleteColumn($i);
        }
        
        // Delete the columns on the right.
        for ($i = $right + 1; $i < $this->getNumberOfColumns();) {
            $this->deleteColumn($i);
        }
    }
    
    /**
     * 
     * @return ???
     */
    public function extractLastString($column, $breakPoint = ' ')
    {
        
        // Adds a column to the end.
        $this->addColumn();
        $index = $this->getNumberOfColumns() - 1;
        
        foreach ($this->data as $iRow => &$row) {
            $row[$index] = substr($row[$column], strrpos($row[$column], $breakPoint) + 1);
            $row[$column] = substr($row[$column], 0, strrpos($row[$column], $breakPoint) - strlen($row[$column]));
        }
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
    
    public function removeEmptyRows()
    {
        foreach ($this->data as $iRow => $row) {
            $isEmpty = true;
            foreach ($row as $data) {
                if ($data) {
                    $isEmpty = false;
                    break;
                }
            }
            if ($isEmpty) {
                unset($this->data[$iRow]);
            }
        }
        $this->data = array_values($this->data);
    }
    
    public function removeWhitespace($column)
    {
        if ($this->isValidColumn($column)) {
            foreach ($this->data as &$row) {
                $row[$column] = preg_replace('/\s+/', '', $row[$column]);
            }
        }
    }

/**
 * Remove a field from the rows of data.
 * DO TG Feature: Remove multiple rows
 * 
 * @param array  $data
 * @param string $field
 * 
 * @return array
 */
function removeField(array $data, $field)
{
    foreach ($data as &$row) {
        unset($row[$field]);
    }
    return $data;
}

/**
 * Add a field in the rows of data.
 * 
 * @param array  $data
 * @param string $field
 * @param mixed  $value
 * 
 * @return array
 */
function addField(array $data, $field, $value)
{
    foreach ($data as &$row) {
        $row[$field] = $value;
    }
    return $data;
}

/**
 * DO TG Develop: Add more extensive criteria.
 * 
 * Get a field value from a specific row in a collection of rows.
 * 
 * @param int|string  $field
 * @param array       $criteria
 * @param string|null $default
 * 
 * @return string|null
 * 
 * @throws \Exception
 */
public function getRowValue($field, array $criteria, $default = null)
{
    foreach ($this->data as $row) {
        foreach ($criteria as $f => $v) {
            if ($row[$f] == $v) {
                return $row[$field];
            }
        }
    }
    return $default;
}

    public function trimData()
    {
        foreach ($this->data as &$row) {
            foreach ($row as &$data) {
                $data = trim($data);
            }
        }
    }

/**
 * 
 * @param array  $data
 * @param string $field
 * @param mixed  $oldValue
 * @param mixed  $newValue
 * 
 * @return array
 */
function updateField(array $data, $field, $oldValue, $newValue)
{
    foreach ($data as &$row) {
        if ($row[$field] == $oldValue) {
            $row[$field] = $newValue;
        }
    }
    return $data;
}

    /**
     * Transform the data set between forms.
     * 
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
     * Check that the column exists in the dataset.
     * Has the side effect of converting $column from an integer index into a
     * string key for associative arrays.
     * 
     * @param int|string $column
     * 
     * @return boolean True if the column exists in the dataset.
     * 
     * @throws \InvalidArgumentException|\Exception
     */
    protected function isValidColumn(&$column)
    {
        if (count($this->data) === 0) {
            throw new \Exception('The dataset is empty');
        }
        
        if (is_int($column)) {
            if ($column < count($this->data[0])) {
                
                // Convert the valid column index to the field key to cover both
                // indexed and associative arrays.
                $column = array_keys($this->data[0])[$column];
            } else {
                throw new \InvalidArgumentException(
                    'Column index "'.$column.'" does not exist in dataset'
                );
            }
        } elseif (!array_key_exists($column, $this->data[0])) {
            if (is_numeric($column)) {
                throw new \InvalidArgumentException(
                    'Unknown field "'.$column.'" in dataset; did you mean index'
                    . ' '.$column.'?'
                );
            } else {
                throw new \InvalidArgumentException(
                    'Unknown field "'.$column.'" in dataset'
                );
            }
        }
        
        return true;
    }
    
    /**
     * Check that $row is a valid integer and the row exists.
     * 
     * @param int $row
     * 
     * @return boolean True if the row exists in the dataset.
     * 
     * @throws \InvalidArgumentException|\Exception
     */
    protected function isValidRow($row)
    {
        if (count($this->data) === 0) {
            throw new \Exception('The dataset is empty');
        }
        
        if (is_int($row)) {
            if ($row < count($this->data)) {
                return true;
            } else {
                throw new \InvalidArgumentException(
                    'Row index "'.$row.'" does not exist in dataset'
                );
            }
        }
        throw new \InvalidArgumentException('$row must be an integer');
    }
}

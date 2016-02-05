<?php

/*
 * Copyright (c) 2015 Greasy Lab.
 * 
 * Tom Gray
 * 
 * Date: 25th September 2015
 */

namespace GreasyLab\Library;

require '../src/DataSet.php';

/**
 * Test cases for DataSet.php
 *
 * @version 1.0 25th September 2015
 * @author Tom Gray
 * @copyright Greasy Lab
 */
class DataSetTest extends \PHPUnit_Framework_TestCase
{
    /**
     * An empty dataset to perform tests on.
     * 
     * @var DataSet
     */
    protected $emptyDataSet;
    
    /**
     * A dataset to perform tests on where the rows are indexed arrays.
     * 
     * @var DataSet
     */
    protected $indexedDataSet;
    
    /**
     * A dataset to perform tests on where the rows are associative arrays and
     * the values are indexed by their field names.
     * 
     * @var DataSet
     */
    protected $associativeDataSet;
    
    /**
     * Create datasets for testing.
     * 
     * @before
     */
    public function setUp()
    {
        $this->emptyDataSet = new DataSet([]);
        
        $this->indexedDataSet = new DataSet(
            [
                ['Tom', 'Gray', 'Mr', 'Harry'],
                ['Alex', 'James', 'Mr', 'Larry'],
                ['Greg', 'Matthews', 'Mr', 'Dick'],
                ['Bert', 'Eagle', 'Mr', 'William']
            ]
        );
        
        $this->associativeDataSet = new DataSet(
            [
                [
                    'firstName' => 'Alex',
                    'lastName' => 'James',
                    'title' => 'Mr',
                    'middleName' => 'Larry'
                ],
                [
                    'firstName' => 'Tom',
                    'lastName' => 'Gray',
                    'title' => 'Mr',
                    'middleName' => 'Harry'
                ],
                [
                    'firstName' => 'Greg',
                    'lastName' => 'Matthews',
                    'title' => 'Mr',
                    'middleName' => 'Dick'
                ],
                [
                    'firstName' => 'Bert',
                    'lastName' => 'Eagle',
                    'title' => 'Mr',
                    'middleName' => 'William'
                ]
            ]
        );
    }
    
    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Row index 2 has less fields than the first row
     */
    public function constructorThrowsExceptionWhenRowsShorterInLength()
    {
        $dataSet = new DataSet(
            [
                ['firstName' => 'Alex', 'lastName' => 'James'],
                ['firstName' => 'Tom', 'lastName' => 'Gray'],
                ['firstName' => 'Greg'],
                ['firstName' => 'Bert', 'lastName' => 'Eagle']
            ]
        );
    }
    
    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Row index 3 has more fields than the first row
     */
    public function constructorThrowsExceptionWhenRowsLongerInLength()
    {
        $dataSet = new DataSet(
            [
                ['firstName' => 'Alex', 'lastName' => 'James'],
                ['firstName' => 'Tom', 'lastName' => 'Gray'],
                ['firstName' => 'Greg', 'lastName' => 'Matthews'],
                ['firstName' => 'Bert', 'lastName' => 'Eagle', 'title' => 'Mr']
            ]
        );
    }
    
    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Row index 1 has a field "title" that does not
     */
    public function constructorThrowsExceptionWhenRowFieldsDiffer()
    {
        $dataSet = new DataSet(
            [
                ['firstName' => 'Alex', 'lastName' => 'James'],
                ['firstName' => 'Tom', 'title' => 'Mr'],
                ['firstName' => 'Greg', 'lastName' => 'Matthews'],
                ['firstName' => 'Bert', 'lastName' => 'Eagle', 'title' => 'Mr']
            ]
        );
    }
    
    /**
     * @test
     */
    public function getNumberOfRowsGivesCorrectNumber()
    {
        $this->assertEquals(
            0,
            $this->emptyDataSet->getNumberOfRows(),
            'Number of rows in empty dataset should be 0'
        );
        
        $this->assertEquals(
            4,
            $this->indexedDataSet->getNumberOfRows(),
            'Number of rows in indexed dataset should be 4'
        );
        
        $this->assertEquals(
            4,
            $this->associativeDataSet->getNumberOfRows(),
            'Number of rows in associative dataset should be 4'
        );
    }
    
    /**
     * @test
     */
    public function getNumberOfColumnsGivesCorrectNumber()
    {
        $this->assertEquals(
            0,
            $this->emptyDataSet->getNumberOfColumns(),
            'Number of columns in empty dataset should be 0'
        );
        
        $this->assertEquals(
            4,
            $this->indexedDataSet->getNumberOfColumns(),
            'Number of columns in indexed dataset should be 2'
        );
        
        $this->assertEquals(
            4,
            $this->associativeDataSet->getNumberOfColumns(),
            'Number of columns in associative dataset should be 2'
        );
    }
    
    /**
     * @test
     * @expectedException \Exception
     * @expectedExceptionMessage The dataset is empty
     */
    public function getRowThrowsExceptionWhenDataSetIsEmpty()
    {
        $this->emptyDataSet->getRow(0);
    }
    
    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage $row must be an integer
     */
    public function getRowThrowsInvalidArgumentExceptionWhenRowIsNotInt()
    {
        $this->associativeDataSet->getRow('3');
    }
    
    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Row index "7" does not exist in dataset
     */
    public function getRowThrowsInvalidArgumentExceptionWhenRowDoesNotExist()
    {
        $this->associativeDataSet->getRow(7);
    }
    
    /**
     * @test
     */
    public function getRowGivesCorrectRow()
    {
        $this->assertTrue(
            count($this->indexedDataSet->getRow(1))
                === $this->indexedDataSet->getNumberOfColumns(),
            'Should return second row of indexed dataset containing two values'
        );
        $this->assertContains(
            'Alex',
            $this->indexedDataSet->getRow(1),
            'Should return second row of indexed dataset containing "Alex '
            . 'James"'
        );
        $this->assertContains(
            'James',
            $this->indexedDataSet->getRow(1),
            'Should return second row of indexed dataset containing "Alex '
            . 'James"'
        );
        
        $this->assertTrue(
            count($this->associativeDataSet->getRow(1))
                === $this->associativeDataSet->getNumberOfColumns(),
            'Should return second row of associative dataset containing two '
            . 'values'
        );
        $this->assertContains(
            'Tom',
            $this->associativeDataSet->getRow(1),
            'Should return second row of associative dataset containing "Tom '
            . 'Gray"'
        );
        $this->assertContains(
            'Gray',
            $this->associativeDataSet->getRow(1),
            'Should return second row of associative dataset containing "Tom '
            . 'Gray"'
        );
    }
    
    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Unknown field "randomField" in dataset
     */
    public function getColumnThrowsInvalidArgumentExceptionForUnknownField()
    {
        $this->associativeDataSet->getColumn('randomField');
    }
    
    /**
     * Test that an exception is thrown with a message suggesting that maybe an
     * integer index was meant as the argument when a numerical string field is
     * not in the dataset.
     * 
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Unknown field "3" in dataset; did you mean
     */
    public function getColumnExceptionMessageSuggestsIntUnknownNumericalField()
    {
        $this->associativeDataSet->getColumn('3');
    }
    
    /**
     * Test that an exception is thrown when there are no columns in the
     * dataset.
     * 
     * @test
     * @expectedException \Exception
     * @expectedExceptionMessage The dataset is empty
     */
    public function getColumnThrowsExceptionWhenDataSetEmpty()
    {
        $this->emptyDataSet->getColumn(0);
    }
    
    /**
     * Test that an exception is thrown when there is no column of the specified
     * index in the dataset.
     * 
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Column index "12" does not exist in dataset
     */
    public function getColumnThrowsInvalidArgumentExceptionWhenNoColumnIndex()
    {
        $this->indexedDataSet->getColumn(12);
    }
    
    /**
     * @test
     */
    public function getColumnGivesCorrectColumn()
    {
        $this->assertTrue(
            count($this->indexedDataSet->getColumn(1)) === 4,
            'Should return second column of indexed dataset containing four '
            . 'values'
        );
        $this->assertContains(
            'Matthews',
            $this->indexedDataSet->getColumn(1),
            'Should return second column of indexed dataset containing '
            . '"Matthews"'
        );
        $this->assertContains(
            'Gray',
            $this->indexedDataSet->getColumn(1),
            'Should return second column of indexed dataset containing "Gray"'
        );
        
        $this->assertTrue(
            count($this->associativeDataSet->getColumn(1)) === 4,
            "Should return second column of associative array containing four "
            . "items"
        );
        $this->assertContains(
            'Eagle',
            $this->associativeDataSet->getColumn(1),
            'Should return second column of associative array containing '
            . '"Eagle"'
        );
        $this->assertContains(
            'James',
            $this->associativeDataSet->getColumn(1),
            'Should return second column of associative array containing '
            . '"James"'
        );
        
        $this->assertTrue(
            count($this->associativeDataSet->getColumn('lastName')) === 4,
            "Should return second column of associative array containing four "
            . "items"
        );
        $this->assertContains(
            'Eagle',
            $this->associativeDataSet->getColumn('lastName'),
            'Should return second column of associative array containing '
            . '"Eagle"'
        );
        $this->assertContains(
            'James',
            $this->associativeDataSet->getColumn('lastName'),
            'Should return second column of associative array containing '
            . '"James"'
        );
    }
    
    /**
     * @test
     * @expectedException \Exception
     * @expectedExceptionMessage The dataset is empty
     */
    public function getValueThrowsExceptionWhenDataSetEmpty()
    {
        $this->emptyDataSet->getValue(5, 7);
    }
    
    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage $row must be an integer
     */
    public function getValueThrowsInvalidArgumentExceptionWhenRowNotAnInteger()
    {
        $this->associativeDataSet->getValue('5', 'firstName');
    }
    
    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Row index "10" does not exist in dataset
     */
    public function getValueThrowsInvalidArgumentExceptionWhenRowDoesNotExist()
    {
        $this->associativeDataSet->getValue(10, 'firstName');
    }
    
    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Column index "12" does not exist in dataset
     */
    public function getValueThrowsInvalidArgumentExceptionWhenNoColumnIndex()
    {
        $this->indexedDataSet->getValue(2, 12);
    }
    
    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Unknown field "username" in dataset
     */
    public function getValueThrowsInvalidArgumentExceptionWhenUnknownField()
    {
        $this->associativeDataSet->getValue(2, 'username');
    }
    
    /**
     * @test
     */
    public function getValueGivesCorrectValue()
    {
        $this->assertEquals(
            'Bert',
            $this->indexedDataSet->getValue(3, 0),
            'Should return "Bert" from the first column of the last row'
        );
        
        $this->assertEquals(
            'Bert',
            $this->associativeDataSet->getValue(3, 0),
            'Should return "Bert" from the first column of the last row'
        );
        
        $this->assertEquals(
            'Bert',
            $this->associativeDataSet->getValue(3, 'firstName'),
            'Should return "Bert" from the first column of the last row'
        );
    }
    
    /**
     * @test
     * @expectedException \Exception
     * @expectedExceptionMessage The dataset is empty
     */
    public function deleteRowThrowsExceptionWhenDataSetIsEmpty()
    {
        $this->emptyDataSet->deleteRow(3);
    }
    
    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage $row must be an integer
     */
    public function deleteRowThrowsInvalidArgumentExceptionWhenRowNotAnInteger()
    {
        $this->associativeDataSet->deleteRow('6');
    }
    
    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Row index "10" does not exist in dataset
     */
    public function deleteRowThrowsInvalidArgumentExceptionWhenRowDoesNotExist()
    {
        $this->indexedDataSet->deleteRow(10);
    }
    
    /**
     * @test
     */
    public function deleteRowDeletesTheCorrectRow()
    {
        $this->indexedDataSet->deleteRow(1);
        
        $this->assertEquals(
            3,
            $this->indexedDataSet->getNumberOfRows(),
            'Dataset should now only have 3 rows as one has been deleted'
        );
        
        $this->assertContains(
            'Tom',
            $this->indexedDataSet->getRow(0),
            'Row 0 should contain "Tom Gray"'
        );
        $this->assertContains(
            'Gray',
            $this->indexedDataSet->getRow(0),
            'Row 0 should contain "Tom Gray"'
        );
        
        $this->assertContains(
            'Greg',
            $this->indexedDataSet->getRow(1),
            'Row 1 should contain "Greg Matthews"'
        );
        $this->assertContains(
            'Matthews',
            $this->indexedDataSet->getRow(1),
            'Row 1 should contain "Greg Matthews"'
        );
        
        $this->assertContains(
            'Bert',
            $this->indexedDataSet->getRow(2),
            'Row 2 should contain "Bert Eagle"'
        );
        $this->assertContains(
            'Eagle',
            $this->indexedDataSet->getRow(2),
            'Row 2 should contain "Bert Eagle"'
        );
    }
    
    /**
     * @test
     */
    public function deleteRowsDeletesTheCorrectRows()
    {
        $rows = array(0, 2);
        $this->associativeDataSet->deleteRows($rows);
        
        $this->assertEquals(
            2,
            $this->associativeDataSet->getNumberOfRows(),
            'Should have 2 rows left after deleting 2 rows'
        );
        
        $this->assertContains(
            'Tom',
            $this->associativeDataSet->getRow(0),
            'Row 0 should contain "Tom Gray"'
        );
        $this->assertContains(
            'Gray',
            $this->associativeDataSet->getRow(0),
            'Row 0 should contain "Tom Gray"'
        );
        
        $this->assertContains(
            'Bert',
            $this->associativeDataSet->getRow(1),
            'Row 0 should contain "Bert Eagle"'
        );
        $this->assertContains(
            'Eagle',
            $this->associativeDataSet->getRow(1),
            'Row 0 should contain "Bert Eagle"'
        );
    }
    
    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Unknown field "randomField" in dataset
     */
    public function deleteColumnThrowsInvalidArgumentExceptionForUnknownField()
    {
        $this->associativeDataSet->deleteColumn('randomField');
    }
    
    /**
     * Test that an exception is thrown with a message suggesting that maybe an
     * integer index was meant as the argument when a numerical string field is
     * not in the dataset.
     * 
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Unknown field "3" in dataset; did you mean
     */
    public function deleteColumnExceptionMessageSuggestsAnIntegerColumnIndex()
    {
        $this->associativeDataSet->deleteColumn('3');
    }
    
    /**
     * Test that an exception is thrown when there are no columns in the
     * dataset.
     * 
     * @test
     * @expectedException \Exception
     * @expectedExceptionMessage The dataset is empty
     */
    public function deleteColumnThrowsExceptionWhenDataSetEmpty()
    {
        $this->emptyDataSet->deleteColumn(0);
    }
    
    /**
     * Test that an exception is thrown when there is no column of the specified
     * index in the dataset.
     * 
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Column index "12" does not exist in dataset
     */
    public function deleteColumnThrowsInvalidArgumentExceptionWhenNoColumn()
    {
        $this->indexedDataSet->deleteColumn(12);
    }
    
    /**
     * @test
     */
    public function deleteColumnFieldDeletesCorrectColumn()
    {
        $nRows = $this->associativeDataSet->getNumberOfRows();
        $nColumns = $this->associativeDataSet->getNumberOfColumns();
        
        $this->associativeDataSet->deleteColumn('lastName');
        
        $this->assertEquals(
            $nRows,
            $this->associativeDataSet->getNumberOfRows(),
            'Should still have the same number of rows as column has been '
            . 'deleted'
        );
        $this->assertEquals(
            $nColumns - 1,
            $this->associativeDataSet->getNumberOfColumns(),
            'Should have one less column as a column has been deleted'
        );
        
        foreach ($this->associativeDataSet->getData() as $row) {
            $this->assertEquals(
                $nColumns - 1,
                count($row),
                'Each row should have one less column as a column has been '
                . 'deleted'
            );
            $this->assertContains(
                'firstName',
                array_keys($row),
                'Should still contain the column "firstName"'
            );
            $this->assertContains(
                'title',
                array_keys($row),
                'Should still contain the column "title"'
            );
            $this->assertNotContains(
                'lastName',
                array_keys($row),
                'Should not contain the column "lastName" as this has been '
                . 'deleted'
            );
        }
    }
    
    /**
     * @test
     */
    public function deleteColumnIndexDeletesCorrectColumn()
    {
        $nRows = $this->indexedDataSet->getNumberOfRows();
        $nColumns = $this->indexedDataSet->getNumberOfColumns();
        
        $this->indexedDataSet->deleteColumn(1);
        
        $this->assertEquals(
            $nRows,
            $this->indexedDataSet->getNumberOfRows(),
            'Should still have the same number of rows as column has been '
            . 'deleted'
        );
        $this->assertEquals(
            $nColumns - 1,
            $this->indexedDataSet->getNumberOfColumns(),
            'Should have one less column as a column has been deleted'
        );
        
        foreach ($this->indexedDataSet->getData() as $row) {
            $this->assertEquals(
                $nColumns - 1,
                count($row),
                'Each row should have one less column as a column has been '
                . 'deleted'
            );
            $this->assertContains(
                '0',
                array_keys($row),
                'Should still contain column 0'
            );
            $this->assertContains(
                '2',
                array_keys($row),
                'Should still contain column 2'
            );
            $this->assertNotContains(
                '1',
                array_keys($row),
                'Should not contain column 1 as this has been deleted'
            );
        }
    }
    
    /**
     * @test
     */
    public function deleteColumnsWithIndexesDeletesTheCorrectColumns()
    {
        // Get starting number of rows and columns.
        $nColumns = $this->indexedDataSet->getNumberOfColumns();
        $nRows = $this->indexedDataSet->getNumberOfRows();
        
        // Delete columns.
        $columns = array(0, 2);
        $this->indexedDataSet->deleteColumns($columns);
        
        // Assert the correct number of rows and columns after delete.
        $this->assertEquals(
            $nColumns - 2,
            $this->indexedDataSet->getNumberOfColumns(),
            'Should have '.($nColumns - count($columns))
            . ' columns left after deleting '
            . count($columns) . ' columns'
        );
        $this->assertEquals(
            $nRows,
            $this->indexedDataSet->getNumberOfRows(),
            'Should have '.$nRows.' rows left after deleting '.count($columns)
            . ' columns'
        );
        
        // Assert the correct number of columns in each row after delete.
        foreach ($this->indexedDataSet->getData() as $row) {
            $this->assertEquals(
                $nColumns - count($columns),
                count($row),
                'Each row should have '.($nColumns - count($columns))
                . ' columns left after deleting ' . count($columns) . ' columns'
            );
        }
        
        // Assert the correct columns were deleted.
        $this->assertContains(
            'Gray',
            $this->indexedDataSet->getRow(0),
            'Row 0 should contain "Gray" and "Harry"'
        );
        $this->assertContains(
            'Harry',
            $this->indexedDataSet->getRow(0),
            'Row 0 should contain "Gray" and "Harry"'
        );
        
        $this->assertContains(
            'James',
            $this->indexedDataSet->getRow(1),
            'Row 1 should contain "James" and "Larry"'
        );
        $this->assertContains(
            'Larry',
            $this->indexedDataSet->getRow(1),
            'Row 1 should contain "James" and "Larry"'
        );
        
        $this->assertContains(
            'Matthews',
            $this->indexedDataSet->getRow(2),
            'Row 2 should contain "Matthews" and "Dick"'
        );
        $this->assertContains(
            'Matthews',
            $this->indexedDataSet->getRow(2),
            'Row 2 should contain "Matthews" and "Dick"'
        );
        
        $this->assertContains(
            'Eagle',
            $this->indexedDataSet->getRow(3),
            'Row 3 should contain "Eagle" and "William"'
        );
        $this->assertContains(
            'Eagle',
            $this->indexedDataSet->getRow(3),
            'Row 3 should contain "Eagle" and "William"'
        );
    }
    
    /**
     * @test
     */
    public function deleteColumnsWithFieldNamesDeletesTheCorrectColumns()
    {
        // Get starting number of rows and columns.
        $nColumns = $this->associativeDataSet->getNumberOfColumns();
        $nRows = $this->associativeDataSet->getNumberOfRows();
        
        // Delete columns.
        $columns = array('firstName', 'title');
        $this->associativeDataSet->deleteColumns($columns);
        
        // Assert the correct number of rows and columns after delete.
        $this->assertEquals(
            $nColumns - 2,
            $this->associativeDataSet->getNumberOfColumns(),
            'Should have '.($nColumns - count($columns))
            . ' columns left after deleting '
            . count($columns) . ' columns'
        );
        $this->assertEquals(
            $nRows,
            $this->associativeDataSet->getNumberOfRows(),
            'Should have '.$nRows.' rows left after deleting '.count($columns)
            . ' columns'
        );
        
        // Assert the correct number of columns in each row after delete.
        foreach ($this->associativeDataSet->getData() as $row) {
            $this->assertEquals(
                $nColumns - count($columns),
                count($row),
                'Each row should have '.($nColumns - count($columns))
                . ' columns left after deleting ' . count($columns) . ' columns'
            );
        }
        
        // Assert the correct columns were deleted.
        $this->assertContains(
            'James',
            $this->associativeDataSet->getRow(0),
            'Row 0 should contain "James" and "Larry"'
        );
        $this->assertContains(
            'Larry',
            $this->associativeDataSet->getRow(0),
            'Row 0 should contain "James" and "Larry"'
        );
        
        $this->assertContains(
            'Gray',
            $this->associativeDataSet->getRow(1),
            'Row 1 should contain "Gray" and "Harry"'
        );
        $this->assertContains(
            'Harry',
            $this->associativeDataSet->getRow(1),
            'Row 1 should contain "Gray" and "Harry"'
        );
        
        $this->assertContains(
            'Matthews',
            $this->associativeDataSet->getRow(2),
            'Row 2 should contain "Matthews" and "Dick"'
        );
        $this->assertContains(
            'Matthews',
            $this->associativeDataSet->getRow(2),
            'Row 2 should contain "Matthews" and "Dick"'
        );
        
        $this->assertContains(
            'Eagle',
            $this->associativeDataSet->getRow(3),
            'Row 3 should contain "Eagle" and "William"'
        );
        $this->assertContains(
            'Eagle',
            $this->associativeDataSet->getRow(3),
            'Row 3 should contain "Eagle" and "William"'
        );
    }
    
    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Unknown field "randomField" in dataset
     */
    public function getDistinctColumnValuesThrowsExceptionForUnknownField()
    {
        $this->associativeDataSet->getDistinctColumnValues('randomField');
    }
    
    /**
     * Test that an exception is thrown with a message suggesting that maybe an
     * integer index was meant as the argument when a numerical string field is
     * not in the dataset.
     * 
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Unknown field "3" in dataset; did you mean
     */
    public function getDistinctColumnValuesExceptionMessageSuggestsInt()
    {
        $this->associativeDataSet->getDistinctColumnValues('3');
    }
    
    /**
     * Test that an exception is thrown when there are no columns in the
     * dataset.
     * 
     * @test
     * @expectedException \Exception
     * @expectedExceptionMessage The dataset is empty
     */
    public function getDistinctColumnValuesThrowsExceptionWhenDataSetEmpty()
    {
        $this->emptyDataSet->getDistinctColumnValues(0);
    }
    
    /**
     * Test that an exception is thrown when there is no column of the specified
     * index in the dataset.
     * 
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Column index "12" does not exist in dataset
     */
    public function getDistinctColumnValuesThrowsExceptionWhenNoColumnIndex()
    {
        $this->indexedDataSet->getDistinctColumnValues(12);
    }
    
    /**
     * @test
     */
    public function getDistinctColumnValuesGivesCorrectValues()
    {
        $values = $this->associativeDataSet->getDistinctColumnValues('title');
        $this->assertTrue(
            count($values) === 1 && in_array('Mr', $values),
            'Should be one distinct column value "Mr"'
        );
        
        $values = $this->indexedDataSet->getDistinctColumnValues(0);
        $this->assertTrue(
            count($values) === 4
            && in_array('Alex', $values)
            && in_array('Tom', $values)
            && in_array('Bert', $values)
            && in_array('Greg', $values),
            'Should be four distinct column values, "Alex", "Tom", "Bert", '
            . '"Greg"'
        );
    }
    
    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Unknown field "randomField" in dataset
     */
    public function getValueCountThrowsInvalidArgumentExceptionForUnknownField()
    {
        $this->associativeDataSet->getValueCount('randomField', 'value');
    }
    
    /**
     * Test that an exception is thrown with a message suggesting that maybe an
     * integer index was meant as the argument when a numerical string field is
     * not in the dataset.
     * 
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Unknown field "3" in dataset; did you mean
     */
    public function getValueCountExceptionMessageSuggestsIntUnknownNumberField()
    {
        $this->associativeDataSet->getValueCount('3', 'value');
    }
    
    /**
     * Test that an exception is thrown when there are no columns in the
     * dataset.
     * 
     * @test
     * @expectedException \Exception
     * @expectedExceptionMessage The dataset is empty
     */
    public function getValueCountThrowsExceptionWhenDataSetEmpty()
    {
        $this->emptyDataSet->getValueCount(0, 'value');
    }
    
    /**
     * Test that an exception is thrown when there is no column of the specified
     * index in the dataset.
     * 
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Column index "12" does not exist in dataset
     */
    public function getValueCountThrowsExceptionWhenNoColumnIndex()
    {
        $this->indexedDataSet->getValueCount(12, 'value');
    }
    
    /**
     * @test
     */
    public function getValueCountGivesCorrectCount()
    {
        $this->assertTrue(
            $this->indexedDataSet->getValueCount(2, 'Mr') === 4,
            'Should be four counts of value "Mr" in column'
        );
        $this->assertTrue(
            $this->associativeDataSet->getValueCount('title', 'Mr') === 4,
            'Should be four counts of value "Mr" in column'
        );
        $this->assertTrue(
            $this->associativeDataSet->getValueCount('firstName', 'Tom') === 1,
            'Should be one count of value "Tom" in column'
        );
    }
    
    /**
     * @test
     * @expectedException \Exception
     * @expectedExceptionMessage The dataset is empty
     */
    public function insertValueThrowsExceptionWhenDataSetEmpty()
    {
        $this->emptyDataSet->insertValue(5, 7, 'newValue');
    }
    
    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage $row must be an integer
     */
    public function insertValueThrowsInvalidArgumentExceptionWhenRowNotAnInt()
    {
        $this->associativeDataSet->insertValue('5', 'firstName', 'newValue');
    }
    
    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Row index "10" does not exist in dataset
     */
    public function insertValueThrowsExceptionWhenRowDoesNotExist()
    {
        $this->associativeDataSet->insertValue(10, 'firstName', 'newValue');
    }
    
    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Column index "12" does not exist in dataset
     */
    public function insertValueThrowsInvalidArgumentExceptionWhenNoColumnIndex()
    {
        $this->indexedDataSet->insertValue(2, 12, 'newValue');
    }
    
    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Unknown field "username" in dataset
     */
    public function insertValueThrowsInvalidArgumentExceptionWhenUnknownField()
    {
        $this->associativeDataSet->insertValue(2, 'username', 'newValue');
    }
    
    /**
     * @test
     */
    public function insertValueInsertsCorrectValueAtCorrectLocation()
    {
        $row = 2;
        $column = 2;
        $value = 'Miss';
        
        $oldData = array();
        foreach ($this->indexedDataSet->getData() as $data) {
            $oldData[] = $data;
        }
        
        $this->indexedDataSet->insertValue($row, $column, $value);
        
        // Assert the value has been inserted.
        $this->assertContains(
            $value,
            $this->indexedDataSet->getRow($row),
            'Column value should have been updated by the insert'
        );
        
        // Assert the rest of the dataset has remained unchanged.
        foreach ($oldData as $index => $data) {
            if ($index !== $row) {
                $this->assertEquals(
                    $data,
                    $this->indexedDataSet->getRow($index),
                    'Row data should be unchanged by the insert'
                );
            }
        }
    }
    
    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Unknown field "randomField" in dataset
     */
    public function replaceValuesThrowsInvalidArgumentExceptionForUnknownField()
    {
        $this->associativeDataSet->replaceValues('randomField', 'old', 'new');
    }
    
    /**
     * Test that an exception is thrown with a message suggesting that maybe an
     * integer index was meant as the argument when a numerical string field is
     * not in the dataset.
     * 
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Unknown field "3" in dataset; did you mean
     */
    public function replaceValuesExceptionMessageSuggestsIntForNumericalField()
    {
        $this->associativeDataSet->replaceValues('3', 'Mr', 'Mrs');
    }
    
    /**
     * Test that an exception is thrown when there are no columns in the
     * dataset.
     * 
     * @test
     * @expectedException \Exception
     * @expectedExceptionMessage The dataset is empty
     */
    public function replaceValuesThrowsExceptionWhenDataSetEmpty()
    {
        $this->emptyDataSet->replaceValues(3, 'Mr', 'Mrs');
    }
    
    /**
     * Test that an exception is thrown when there is no column of the specified
     * index in the dataset.
     * 
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Column index "12" does not exist in dataset
     */
    public function replaceValuesThrowsExceptionWhenNoColumnIndex()
    {
        $this->indexedDataSet->replaceValues(12, 'Mr', 'Mrs');
    }
    
    /**
     * @test
     */
    public function replaceValuesReplacesCorrectValuesInCorrectColumn()
    {
        $column = 'title';
        $oldValue = 'Mr';
        $newValue = 'Mrs';
        
        $oldData = $this->associativeDataSet->getData();
        
        $this->associativeDataSet->replaceValues($column, $oldValue, $newValue);
        
        foreach ($oldData as $row => $data) {
            $newData = $this->associativeDataSet->getRow($row);
            foreach ($data as $field => $value) {
                if ($field === $column) {
                    $this->assertEquals(
                        $newValue,
                        $newData[$field],
                        'New value in column '.$column.' should be '.$newValue
                    );
                } else {
                    $this->assertEquals(
                        $value,
                        $newData[$field],
                        'Values not in column '.$column.' should not have '
                        . 'changed'
                    );
                }
            }
        }
    }
    
    /** DO TG DataSet: Write tests
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Unknown field "randomField" in dataset
     */
    public function replaceValuesThrowsInvalidArgumentExceptionForUnknownField()
    {
        $this->associativeDataSet->replaceValues('randomField', 'old', 'new');
    }
    
    /**
     * Test that an exception is thrown with a message suggesting that maybe an
     * integer index was meant as the argument when a numerical string field is
     * not in the dataset.
     * 
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Unknown field "3" in dataset; did you mean
     */
    public function replaceValuesExceptionMessageSuggestsIntForNumericalField()
    {
        $this->associativeDataSet->replaceValues('3', 'Mr', 'Mrs');
    }
    
    /**
     * Test that an exception is thrown when there are no columns in the
     * dataset.
     * 
     * @test
     * @expectedException \Exception
     * @expectedExceptionMessage The dataset is empty
     */
    public function replaceValuesThrowsExceptionWhenDataSetEmpty()
    {
        $this->emptyDataSet->replaceValues(3, 'Mr', 'Mrs');
    }
    
    /**
     * Test that an exception is thrown when there is no column of the specified
     * index in the dataset.
     * 
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Column index "12" does not exist in dataset
     */
    public function replaceValuesThrowsExceptionWhenNoColumnIndex()
    {
        $this->indexedDataSet->replaceValues(12, 'Mr', 'Mrs');
    }
    
    /**
     * @test
     */
    public function replaceValuesReplacesCorrectValuesInCorrectColumn()
    {
        $column = 'title';
        $oldValue = 'Mr';
        $newValue = 'Mrs';
        
        $oldData = $this->associativeDataSet->getData();
        
        $this->associativeDataSet->replaceValues($column, $oldValue, $newValue);
        
        foreach ($oldData as $row => $data) {
            $newData = $this->associativeDataSet->getRow($row);
            foreach ($data as $field => $value) {
                if ($field === $column) {
                    $this->assertEquals(
                        $newValue,
                        $newData[$field],
                        'New value in column '.$column.' should be '.$newValue
                    );
                } else {
                    $this->assertEquals(
                        $value,
                        $newData[$field],
                        'Values not in column '.$column.' should not have '
                        . 'changed'
                    );
                }
            }
        }
    }
}

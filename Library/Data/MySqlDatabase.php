<?php

/*
 * Copyright (c) 2015 Greasy Lab.
 * 
 * Tom Gray
 * 
 * Date: 8th October 2015
 */

namespace GreasyLab\Library\Data;

require 'AbstractDatabase.php';
require 'DataSet.php';

/**
 * Represents a MySQL database for retrieving datasets.
 * 
 * @version 1.0 8th October 2015
 * @author Tom Gray
 * @copyright 2015 Greasy Lab
 */
class MySqlDatabase extends AbstractDatabase
{
    protected static $operators = [
        '=', 'is', '!=', '<>', 'is not', 'like', '<', '>', 'between'
    ];
    
    protected $host;
    
    /**
     * 
     * 
     * @var \mysqli
     */
    protected $mysqli;
    
    protected $password;
    
    protected $username;
    
    /**
     * Construct the database connection.
     * 
     * @param string $host
     * @param string $username
     * @param string $password
     * @param string $database
     * @param int    $port
     * @param int    $socket
     * 
     * @throws \Exception
     */
    public function __construct(
        $host,
        $username,
        $password,
        $database,
        $port = null,
        $socket = null
    ) {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        
        $this->mysqli = new \mysqli(
            $host,
            $username,
            $password,
            $database,
            $port,
            $socket
        );
        
        // Works with PHP => 5.2.9.
        if ($this->mysqli->connect_error) {
            throw new \Exception(
                'Error connecting to the database: '
                . $this->mysqli->connect_error
            );
        }
        
        // Works with PHP < 5.2.9.
        if (mysqli_connect_error()) {
            throw new \Exception(
                'Error connecting to the database: '
                . mysqli_connect_error()
            );
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function commit()
    {
        if (!$this->mysqli->commit()) {
            throw new \Exception(
                'Error committing transaction: '.$this->mysqli->error
            );
        }
        return true;
    }
    
    /**
     * DO TG Test: This method with recurse.
     * 
     * {@inheritdoc}
     */
    public function delete($table, array $criteria, $recurse = false)
    {
        if ($criteria) {
            $sql = 'delete from `'.$table.'` '.$this->buildCriteria($criteria);
            try {
                return $this->execQuery($sql);
            } catch (\Exception $ex) {
                if (
                    $recurse
                    && strstr(
                        $this->mysqli->error,
                        'Cannot delete or update a parent row: a foreign key '
                        . 'constraint fails'
                    )
                ) {
                    return $this->deleteChildRows($this->mysqli->error, [$sql]);
                }
                throw $ex;
            }
        }
        throw new \Exception('Error deleting record: No criteria was given');
    }
    
    /** DO TG Add to interface and create test.
     * 
     * {@inheritdoc}
     * 
     * @return int|null
     */
    public function findHighestValue($table, $column)
    {
        $result = $this->execQuery(
            'select `'.$column.'` from `'.$table.'` '
            . 'order by `'.$column.'` desc limit 1'
        );
        return $result->fetch_assoc()[$column];
    }
    
    /**
     * {@inheritdoc}
     */
    public function getHost()
    {
        return $this->host;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getPassword()
    {
        return $this->password;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getQueryDataset($query)
    {
        return new DataSet($this->processResult($this->execQuery($query)));
    }
    
    /**
     * {@inheritdoc}
     */
    public function getTableDataset($table, array $criteria = [])
    {
        $sql = 'select * from `'.$table.'` '.$this->buildCriteria($criteria);
        return new DataSet($this->processResult($this->execQuery($sql)));
    }

    /**
     * {@inheritdoc}
     */
    public function getTableMetaData($table)
    {
        $sql = 'show columns from `'.$table.'`;';
        return new DataSet($this->processResult($this->execQuery($sql)));
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        return $this->username;
    }
    
    /**
     * {@inheritdoc}
     */
    public function hasRows($table, array $criteria = [])
    {
        $sql = 'select * from `'.$table.'` '.$this->buildCriteria($criteria);
        $result = $this->execQuery($sql);
        return $result->num_rows > 0;
    }
    
    /**
     * {@inheritdoc}
     */
    public function hasSingleRow($table, array $criteria = [])
    {
        $sql = 'select * from `'.$table.'` '.$this->buildCriteria($criteria);
        $result = $this->execQuery($sql);
        return $result->num_rows == 1;
    }

    /**
     * {@inheritdoc}
     */
    public function insert($table, array $data)
    {
        $this->execQuery($this->buildInsert($table, $data).';');
        return $this->mysqli->insert_id;
    }
    
    /**
     * DO TG Implement: Insert data and ignore existing rows.
     * 
     * {@inheritdoc}
     */
    public function insertIgnore($table, array $data)
    {
        throw new \Exception('Needs to be implemented');
    }

    /**
     * {@inheritdoc}
     */
    public function insertUpdate($table, array $data)
    {
        $sql = $this->buildInsert($table, $data);

        // Build update on duplicate section.
        $sql .= ' on duplicate key update ';
        foreach ($data[0] as $field => $value) {
            $sql .= '`' . $field . '` = VALUES(`'.$field.'`), ';
        }
        $sql = substr($sql, 0, -2); // Remove last comma.
        $sql .= ';';

        $this->execQuery($sql);
        return $this->mysqli->insert_id;
    }
    
    /**
     * {@inheritdoc}
     */
    public function startTransaction()
    {
        if (!$this->mysqli->begin_transaction()) {
            throw new \Exception(
                'Error starting transaction: '.$this->mysqli->error
            );
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function truncate($table)
    {
        $sql = 'truncate '.$table.';';
        if (!$this->mysqli->query($sql)) {
            throw new \Exception(
                'Error truncating table: '.$this->mysqli->error
            );
        }
    }

    /**
     * Set foreign key checks to false.
     * 
     * @throws \Exception
     */
    public function turnOffForeignKeyChecks()
    {
        if (!$this->mysqli->query('set foreign_key_checks = 0;')) {
            throw new \Exception(
                'Error setting foreign key checks to false: '
                . $this->mysqli->error
            );
        }
    }

    /**
     * Set foreign key checks to true.
     * 
     * @throws \Exception
     */
    public function turnOnForeignKeyChecks()
    {
        if (!$this->mysqli->query('set foreign_key_checks = 1;')) {
            throw new \Exception(
                'Error setting foreign key checks to true: '
                . $this->mysqli->error
            );
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function update($table, array $values)
    {
        $sql = 'update `'.$table.'` set ';
        foreach ($values as $field => $value) {
            $sql .= '`' . $field . '` = '.$this->processOperand($value).', ';
        }
        $sql = substr($sql, 0, -2); // Remove last comma.
        $sql .= ';';
        
        return $this->execQuery($sql);
    }
    
    /**
     * DO TG Feature: Allow a simplified set of criteria to be used, i.e.
     *  'field' => 'value', 'field' => 'value'
     * where record has field = value and field = value.
     * 
     * Build and return the where criteria section of a MySQL query string.
     * 
     * @param array $criteria
     * 
     * @return string
     */
    protected function buildCriteria(array $criteria)
    {
        $sql = 'where ';

        foreach ($criteria as $criterion) {
            
            if (isset($criterion['operator'])) {
                $operator = strtolower($criterion['operator']);
                if (!in_array($operator, self::$operators)) {
                    throw new \Exception($operator.' is not a valid operator');
                }
            } else {
                throw new \Exception(
                    'An operator has not been defined for a criterion'
                );
            }

            switch ($operator) {
                case '!=':
                case 'is not':
                case '<>':
                    $logicOperator = ' and ';
                    break;
                default:
                    $logicOperator = ' or ';
            }

            $sql .= '(';
            foreach ($criterion['operands'] as $value) {
                $sql .= '`'.$criterion['field'].'` ';

                if ($this->isNull($value)) {
                    if ($operator == 'is' || $operator == '=') {
                        $operator = 'is';
                    } elseif (
                        $operator == '<>'
                        || $operator == 'is not'
                        || $operator == '!='
                    ) {
                        $operator = 'is not';
                    } else {
                        throw new \Exception(
                            'NULL cannot be used with the "'.$operator
                            . '" operator'
                        );
                    }
                } elseif ($operator == 'is') {
                    $sql .= '=';
                } elseif ($operator == 'is not') {
                    $sql .= '!=';
                } elseif ($operator == 'between') {
                    throw new \Exception(
                        'The "'.$operator.'" operator needs implementing'
                    );
                }
                
                $sql .= $operator.' '.$this->processOperand($value)
                    . $logicOperator;
            }

            // Remove the last logical operator.
            $sql = substr($sql, 0, -strlen($logicOperator));
            $sql .= ') and ';
        }
        $sql = substr($sql, 0, -5); // Remove last "and".
        $sql .= ';';

        return $sql;
    }
    
    /**
     * Build and return the insert values section of a MySQL query string.
     * 
     * @param string $table
     * @param array $data
     * 
     * @return string
     */
    protected function buildInsert($table, array $data)
    {
        $sql = 'insert into '.$table.' (';
        foreach ($data[0] as $field => $value) {
            $sql .= '`' . $field . '`, ';
        }
        $sql = substr($sql, 0, -2); // Remove last comma.
        $sql .= ') values ';

        // Build the data values to insert.
        foreach ($data as $d) {
            $sql .= '(';
            foreach ($d as $value) {
                $sql .= $this->processOperand($value).',';
            }
            $sql = substr($sql, 0, -1); // Remove last comma.
            $sql .= '),';
        }
        $sql = substr($sql, 0, -1); // Remove last comma.
        return $sql;
    }

    /**
     * DO TG Test: This entire method needs testing after refactoring.
     *  Queries get repeated when deleting?
     *  Test on SwiftCase products.
     *  Run tests on each level of recursion and remove redundant queries.
     * DO TG Feature: Produce a complete list of queries that will be run.
     * 
     * Recursively delete child rows where a foreign key constraint prevents the
     * deletion of the parent row.
     * 
     * @param string $error
     * @param array  $sql
     * 
     * @return boolean
     * 
     * @throws \Exception
     */
    protected function deleteChildRows($error, array $sql)
    {
        static $level = 0;
        if ($level > 50) {
            throw new \Exception(
                'deleteChildRows: Hit max nesting level, recursive delete '
                . 'found?'
            );
        }

        $parentTable = substr(
            $error,
            strpos($error, '(') + 1,
            strpos($error, ',') - strpos($error, '(') - 1
        );

        $foreignKey = substr(
            $error,
            strpos($error, 'FOREIGN KEY (') + 13,
            strpos($error, ') REFERENCES') - strpos($error, 'FOREIGN KEY (')
            - 13
        );

        $referencedColumn = substr(
            $error,
            strpos($error, '` (`') + 3,
            strrpos($error, '`))') - strpos($error, '` (`') - 2
        );

        $lastQuery = $sql[count($sql) - 1];
        $sql[] = 'delete from '.$parentTable.' where '.$foreignKey
            .' in (select '.$referencedColumn
            .substr($lastQuery, 6, count($lastQuery) - 2).');';
        $index = count($sql) - 1;

        while ($index > -1) {
            try {
                var_dump($sql);
                println('Exec next query');
                $this->execQuery($sql[$index]);
                println('Success');
                unset($sql[$index--]);
                $sql = array_values($sql);
            } catch (\Exception $ex) {
                println('Failed');
                if (
                    strstr(
                        $this->mysqli->error,
                        'Cannot delete or update a parent row: a foreign key '
                        . 'constraint fails'
                    )
                ) {
                    println('Foreign key constraint, recursing');
                    ++$level;
                    $this->deleteChildRows($this->mysqli->error, $sql);
                    --$level;
                } elseif (
                    strstr(
                        $this->mysqli->error,
                        ' is specified twice, both as a target for '
                    )
                    ||
                    strstr(
                        $this->mysqli->error,
                        'You can\'t specify target table \''
                    )
                ) {
                    println('Recursive reference found here:');
                    println($this->mysqli->error);
                    println('De-referencing query');
                    
                    // Recursive foreign key referencing delete has been found
                    // here.
                    unset($sql[$index--]);

                    // Select referenced column keys for de-referencing.
                    $query = 'select '.$referencedColumn
                        .substr($lastQuery, 6, count($lastQuery) - 2).';';
                    var_dump($query);
                    if ($result = $this->execQuery($query)) {
                        $referencedColumnKeys = '';
                        while ($row = $result->fetch_assoc()) {
                            $referencedColumnKeys .=
                                $row[substr($referencedColumn, 1, -1)].',';
                        }
                        $referencedColumnKeys = substr(
                            $referencedColumnKeys,
                            0,
                            -1
                        );
                        var_dump('Result: '.$referencedColumnKeys);
                        
                        // Select all the rows that are to be de-referenced so
                        // they can be deleted once they have been
                        // de-referenced.
                        // DO TG Improvement: id is specified here, make this
                        // dynamic by selecting on all fields for an exact
                        // match.
                        $query = 'select id from '.$parentTable.' where '
                            .$foreignKey.' in ('.$referencedColumnKeys.');';
                        var_dump($query);
                        if ($result = $this->execQuery($query)) {
                            
                            // De-reference all the rows.
                            $query = 'update '.$parentTable.' set '.$foreignKey
                                .' = NULL where '.$foreignKey.' in ('
                                .$referencedColumnKeys.');';
                            var_dump($query);
                            $this->execQuery($query);
                            
                            // DO TG Implement: Needs another exception catch here for cyclic
                            // referencing rows to de-reference a second time as this
                            // delete throws an exception.
                            // Delete all the de-referenced rows.
                            $query = 'delete from '.$parentTable.' where id in (';
                            while ($row = $result->fetch_assoc()) {
                                $query .= $row['id'].',';
                            }
                            $query = substr($query, 0, -1);
                            $query .= ');';
                            var_dump($query);
                            $this->execQuery($query);
                            println('Done');
                        } else {
                            throw $ex;
                        }
                    }
                } else {
                    throw $ex;
                }
            }
        }
        println('Returning');
        return true;
    }

    /**
     * Perform a query on the database.
     * 
     * @param string $sql
     * 
     * @return \mysqli_result|boolean
     * 
     * @throws \Exception
     */
    protected function execQuery($sql)
    {
        if ($result = $this->mysqli->query($sql)) {
            return $result;
        }
        throw new \Exception('Error executing query: '.$this->mysqli->error);
    }

    /**
     * Process the data, escaping strings and replacing null values as
     * necessary.
     * 
     * @param mixed $data
     * @param mixed $val
     * 
     * @return string
     */
    protected function processOperand($data, $val = null)
    {
        if ($this->isNull($data)) {
            if (null === $val) {
                return 'NULL';
            } elseif ('NOW' === $val) {
                $data = date('Y-m-d H:i:s');
            } else {
                $data = $val;
            }
        }
        return '\''.$this->mysqli->real_escape_string(trim($data)).'\'';
    }

    /**
     * Convert mysqli query result into an array and trim the data.
     * 
     * @param \mysqli_result $result
     * 
     * @return array
     */
    protected function processResult(\mysqli_result $result)
    {
        $data = [];
        while ($row = $result->fetch_assoc()) {
            foreach ($row as &$value) {
                $value = (null !== $value) ? trim($value) : $value;
            }
            $data[] = $row;
        }
        return $data;
    }
}

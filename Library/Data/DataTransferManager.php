<?php

/*
 * Copyright (c) 2015 Greasy Lab.
 * 
 * Friday 4th December 2015
 * 
 * Tom Gray
 */

namespace GreasyLab\DataLib;

use GreasyLab\Library\AbstractDatabase;
use GreasyLab\Library\DataSet;

/**
 * Perform tasks facilitating the transfer of data between databases.
 *
 * @author Tom Gray
 * @version 1.0 Friday 4th December 2015
 */
class DataTransferManager
{
    /**
     * DO TG Feature: Compute ID prefix and add to datasets in blocks for large
     * data sets.
     */
//        $offset = 0;
//        $size = 1000;
//        $dataSet = $this->src->getQueryDataset(
//            'select * from payment_transaction_attribute '
//            . 'limit '.$offset.', '.$size
//        );
//        while (!$dataSet->isEmpty()) {
//            $dataSet->deleteColumn('id');
//            $dataSet->prependValues(
//                'transaction_id',
//                $transIdPrefix
//            );
//            $this->dest->insert('payment_transaction_attribute', $dataSet->getRows());
//            $dataSet = $this->src->getQueryDataset(
//                'select * from payment_transaction_attribute '
//                . 'limit '.$offset.', '.$size
//            );
//        }
    
    /**
     * DO TG Comment
     * 
     * @param AbstractDatabase $dest
     * @param string $table
     * @param string $column
     * @param DataSet $dataSet
     * 
     * @return int
     */
    public function computeIdPrefix(
        AbstractDatabase $dest,
        $table,
        $column,
        DataSet $dataSet
    ) {
        $highestLiveId = $dest->findHighestValue($table, $column);
        $newId = $lowestImportId = $dataSet->getValue(0, $column);
        $prefix = '';
        
        while ($highestLiveId != null && $newId <= $highestLiveId) {
            if (!$prefix) {
                $prefix = 1;
            }
            $newId = $prefix++ . $lowestImportId;
        }
        
        $dataSet->prependValues($column, $prefix);
        
        return $prefix;
    }
}

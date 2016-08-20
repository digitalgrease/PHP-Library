<?php

/*
 * Copyright (c) 2015 Digital Grease Limited.
 */

namespace DigitalGrease\Library\Web;

use DigitalGrease\Library\Utils\StringUtils;

/**
 * Represents a parsed HTML table.
 * 
 * @author Tom Gray
 */
class HtmlTable
{
    const OPEN_TABLE_TAG = '<table';
    const CLOSE_TABLE_TAG = '</table>';
    
    const OPEN_THEAD_TAG = '<thead';
    const CLOSE_THEAD_TAG = '</thead>';
    
    const OPEN_TBODY_TAG = '<tbody';
    const CLOSE_TBODY_TAG = '</tbody>';
    
    const OPEN_TH_TAG = '<th';
    const CLOSE_TH_TAG = '</th>';
    
    const OPEN_TR_TAG = '<tr';
    const CLOSE_TR_TAG = '</tr>';
    
    const OPEN_TD_TAG = '<td';
    const CLOSE_TD_TAG = '</td>';
    
    /**
     * Find all the tables defined in the given HTML.
     * 
     * @param string $html
     * 
     * @return array Collection of HtmlTable objects.
     */
    public static function getTables($html)
    {
        $tables = [];
        
        $blocks = StringUtils::getBlocks(
            $html,
            self::OPEN_TABLE_TAG,
            self::CLOSE_TABLE_TAG
        );
        
        foreach ($blocks as $block) {
            $tables[] = new HtmlTable($block);
        }
        
        return $tables;
    }
    
    /**
     * The complete HTML that represents the table from the opening table tag to
     * the closing table tag inclusive.
     * 
     * @var string
     */
    protected $html;
    
    protected $thead;
    
    protected $tbody;
    
    /**
     * Array of the HTML that defines the table rows.
     * 
     * @var array
     */
    protected $rows;
    
    protected $data;
    
    /**
     * Construct the table.
     * 
     * @param string $html The complete HTML that represents the table from the
     *                     opening table tag to the closing table tag inclusive.
     */
    public function __construct($html)
    {
        // Break down and store the HTML that defines the table.
        $this->html = $html;
        $this->thead = StringUtils::getBlocks(
            $html,
            self::OPEN_THEAD_TAG,
            self::CLOSE_THEAD_TAG
        );
        $this->tbody = StringUtils::getBlocks(
            $html,
            self::OPEN_TBODY_TAG,
            self::CLOSE_TBODY_TAG
        );
        $this->rows = StringUtils::getBlocks(
            $html,
            self::OPEN_TR_TAG,
            self::CLOSE_TR_TAG
        );
        
        // Build the table data from the HTML.
        $this->data = [];
        foreach ($this->rows as $rowHtml) {
            $row = [];
            
            // Extract any table headings from the row.
            foreach (StringUtils::getBlocks(
                $rowHtml,
                self::OPEN_TH_TAG,
                self::CLOSE_TH_TAG
            ) as $dataHtml) {
                $row[] = html_entity_decode(strip_tags(trim($dataHtml)));
                
                // If this heading spans multiple columns then the data array
                // requires padding to keep the number of columns consistent on
                // all rows.
                $nCols = StringUtils::getAttributeValue('colspan', $dataHtml);
                if ($nCols) {
                    for ($i = 1; $i < $nCols; ++$i) {
                        $row[] = '';
                    }
                }
            }
            
            // Extract any table data from the row.
            foreach (StringUtils::getBlocks(
                $rowHtml,
                self::OPEN_TD_TAG,
                self::CLOSE_TD_TAG
            ) as $dataHtml) {
                $row[] = html_entity_decode(strip_tags(trim($dataHtml)));
                
                // If this element spans multiple columns then the data array
                // requires padding to keep the number of columns consistent on
                // all rows.
                $nCols = StringUtils::getAttributeValue('colspan', $dataHtml);
                if ($nCols) {
                    for ($i = 1; $i < $nCols; ++$i) {
                        $row[] = '';
                    }
                }
            }
            $this->data[] = $row;
        }
    }
    
    /**
     * Get the complete HTML that represents the table from the opening table
     * tag to the closing table tag inclusive.
     * 
     * @return string
     */
    public function getHtml()
    {
        return $this->html;
    }
    
    /**
     * 
     * @return string
     */
    public function getThead()
    {
        return $this->thead;
    }
    
    /**
     * 
     * @return string
     */
    public function getTbody()
    {
        return $this->tbody;
    }
    
    /**
     * 
     * @return array
     */
    public function getRows()
    {
        return $this->rows;
    }
    
    /**
     * 
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
}

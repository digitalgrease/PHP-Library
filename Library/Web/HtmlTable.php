<?php

/*
 * Copyright (c) 2015 Greasy Lab.
 */

namespace GreasyLab\Library\Web;

use GreasyLab\Library\Utils\StringUtils;

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
    
    protected $rows;
    
    protected $headings;
    
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
        $this->headings = StringUtils::getBlocks(
            $html,
            self::OPEN_TH_TAG,
            self::CLOSE_TH_TAG
        );
        
        // Build the table headings from the HTML.
        if ($this->headings) {
            foreach ($this->headings as $heading) {
                $headings[] = trim(strip_tags($heading));
                
                // If this heading spans multiple columns then the data array
                // requires padding to keep the number of columns consistent on
                // all rows.
                $nCols = StringUtils::getAttributeValue('colspan', $heading);
                if ($nCols) {
                    for ($i = 1; $i < $nCols; ++$i) {
                        $headings[] = '';
                    }
                }
            }
            $this->data[] = $headings;
        }
        
        // Build the table data from the HTML.
        foreach ($this->rows as $rowHtml) {
            $row = [];
            foreach (StringUtils::getBlocks(
                $rowHtml,
                self::OPEN_TD_TAG,
                self::CLOSE_TD_TAG
            ) as $dataHtml) {
                $row[] = trim(strip_tags($dataHtml));
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
    public function getHeadings()
    {
        return $this->headings;
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

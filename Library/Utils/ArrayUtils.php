<?php

/*
 * 
 * 
 * Thursday 10th November 2016
 * 
 * Tom Gray
 */

namespace DigitalGrease\Library\Utils;

/**
 * 
 *
 * @author Tom Gray
 */
class ArrayUtils
{
    
    public function createCombos()
    {
        // DO TG Implement Algorithm
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

<?php

/*
 * Copyright (c) 2018 Digital Grease Limited.
 * 
 * Thursday 10th May 2018
 * 
 * Tom Gray
 */

namespace DigitalGrease\Xml;

require_once 'XmlNode.php';

/**
 * Extends and adds functionality to the PHP class \XmlReader.
 *
 * @author Tom Gray
 * @version 1.0 Saturday 2nd June 2018
 */
class XmlReader extends \XMLReader
{
    
    /**
     * Builds a new XmlNode from the current node pointed at by the reader.
     * 
     * @return XmlNode
     */
    public function getNode()
    {
        $node = new XmlNode(
            $this->name,
            $this->depth,
            $this->nodeType,
            $this->nodeType(),
            $this->value,
            $this->isEmptyElement,
            $this->attributeCount
        );
        
        while ($this->moveToNextAttribute()) {
            $node->addAttribute($this->name, $this->value);
        }
        
        return $node;
    }
    
    /**
     * Gets all the properties of the current node as a human readable string.
     * 
     * @return string
     */
    public function nodeProperties()
    {
        return
            'Base URI: ' . $this->baseURI . PHP_EOL
            . 'Namespace URI: ' . $this->namespaceURI . PHP_EOL
            . 'Name: ' . $this->name . PHP_EOL
            . 'Local Name: ' . $this->localName . PHP_EOL
            . 'Prefix: ' . $this->prefix . PHP_EOL
            . 'XML Language: ' . $this->xmlLang . PHP_EOL
            
            . 'Node Type: ' . $this->nodeType() . PHP_EOL
            . 'Depth: ' . $this->depth . PHP_EOL
            . 'Value: ' . $this->value . PHP_EOL
            . 'Is Empty Element: ' . ($this->isEmptyElement ? 'Yes' : 'No') . PHP_EOL
            
            . 'Attributes: ' . $this->attributeCount . PHP_EOL
            . 'Is attribute defaulted from DTD: ' . ($this->isDefault ? 'Yes' : 'No') . PHP_EOL
        ;
    }
    
    /**
     * Gets the node type as a human readable string.
     * 
     * @param int $id If not provided then uses the current node type value.
     * 
     * @return string
     * 
     * @throws \Exception
     */
    public function nodeType($id = null)
    {
        if (null === $id) {
            $id = $this->nodeType;
        }
        
        switch ($id) {
            case self::NONE:
                return 'No Node Type';
            case self::ELEMENT:
                return 'Start Element';
            case self::ATTRIBUTE:
                return 'Attribute Node';
            case self::TEXT:
                return 'Text Node';
            case self::CDATA:
                return 'CDATA Node';
            case self::ENTITY_REF:
                return 'Entity Reference Node';
            case self::ENTITY:
                return 'Entity Declaration Node';
            case self::PI:
                return 'Processing Instruction Node';
            case self::COMMENT:
                return 'Comment Node';
            case self::DOC:
                return 'Document Node';
            case self::DOC_TYPE:
                return 'Document Type Node';
            case self::DOC_FRAGMENT:
                return 'Document Fragment Node';
            case self::NOTATION:
                return 'Notation Node';
            case self::WHITESPACE:
                return 'Whitespace Node';
            case self::SIGNIFICANT_WHITESPACE:
                return 'Significant Whitespace Node';
            case self::END_ELEMENT:
                return 'End Element';
            case self::END_ENTITY:
                return 'End Entity';
            case self::XML_DECLARATION:
                return 'XML Declaration Node';
            default:
                throw new \Exception('Unknown Node Type "' . $id . '"');
        }
    }
    
    /**
     * Reads the entire document into a structure of XmlNodes.
     * 
     * @return XmlNode|null The root element.
     */
    public function readDocument()
    {
        $root = null;
        $lastNode = null;
        
        while ($this->genericXmlReader->read()) {
            $node = $this->genericXmlReader->getNode();
            
            switch ($node->nodeType()) {
                case \XMLReader::END_ELEMENT:
                    // End element so node is not added.
                    break;
                
                case \XMLReader::ELEMENT:
                    // Start element so node will be added.
                    if (0 == $node->depth()) {
                        $root = $node;

                    } elseif ($node->depth() == $lastNode->depth()) {
                        $lastNode->parent()->addChild($node->name(), $node);
                        
                    } elseif ($node->depth() > $lastNode->depth()) {
                        $diff = $node->depth() - $lastNode->depth();
                        // DEBUGGING
                        if ($diff > 1) {
                            throw new \Exception(
                                'New node is ' . $diff . ' levels deeper than '
                                . 'the last'
                            );
                        }
                        $lastNode->addChild($node->name(), $node);

                    } elseif ($node->depth() < $lastNode->depth()) {
                        $diff = $lastNode->depth() - $node->depth();
                        $lastNode = $lastNode->parent();
                        for ($i = 0; $i < $diff; ++$i) {
                            $lastNode = $lastNode->parent();
                        }
                        $lastNode->addChild($node->name(), $node);
                    }

                    $lastNode = $node;
                    break;
                    
                default:
                    throw new \Exception(
                        'Node type "'
                        . $node->nodeType() . ' => ' . $node->nodeTypeName()
                        . '" not accounted for!'
                    );
            }
        }
        
        return $root;
    }
}

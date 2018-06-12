<?php

/*
 * Copyright (c) 2018 Digital Grease Limited.
 * 
 * Tuesday 29th May 2018
 * 
 * Tom Gray
 */

namespace DigitalGrease\Xml;

/**
 * Represents a node or element from an XML document.
 *
 * @author Tom Gray
 * @version 1.0 Saturday 2nd June 2018
 */
class XmlNode
{
    
    /**
     * The name of this node.
     *
     * @var string
     */
    protected $name;
    
    /**
     * The depth of this node in the structure.
     *
     * @var int
     */
    protected $depth;
    
    /**
     * The type of this node as an integer constant as defined by the constants
     * in \XmlReader.
     *
     * @var int
     */
    protected $nodeType;
    
    /**
     * The type of this node as a human readable string.
     *
     * @var string
     */
    protected $nodeTypeName;
    
    /**
     * The value of this node, if any.
     *
     * @var string
     */
    protected $value;
    
    /**
     * Flag that indicates whether this node/element is empty.
     * 
     * @var boolean
     */
    protected $isEmptyElement;
    
    /**
     * The number of attributes this node has.
     *
     * @var int
     */
    protected $attributeCount;
    
    /**
     * An associative array of key value pairs that are the attributes of this
     * node.
     *
     * @var string[]
     */
    protected $attributes = [];
    
    /**
     * The parent node of this node, if it has one.
     *
     * @var XmlNode
     */
    protected $parent;
    
    /**
     * The child nodes of this node, if any.
     *
     * @var XmlNode[]
     */
    protected $children = [];
    
    /**
     * Compares the names and attributes of all the nodes in each array and
     * returns true only if all the values are the same.
     * 
     * @param XmlNode[] $a
     * @param XmlNode[] $b
     * 
     * @return boolean
     */
    public static function areEqual(array $a, array $b)
    {
        if (count($a) != count($b)) {
            return false;
        }
        
        foreach ($a as $i => $node) {
            if (!$node->isEqual($b[$i])) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Construct a node.
     * 
     * @param string $name
     * @param int $depth
     * @param int $nodeType
     * @param string $nodeTypeName
     * @param string $value
     * @param boolean $isEmptyElement
     * @param int $attributeCount
     */
    public function __construct(
        $name,
        $depth,
        $nodeType,
        $nodeTypeName,
        $value,
        $isEmptyElement,
        $attributeCount
    ) {
        $this->name = $name;
        $this->depth = $depth;
        $this->nodeType = $nodeType;
        $this->nodeTypeName = $nodeTypeName;
        $this->value = $value;
        $this->isEmptyElement = $isEmptyElement;
        $this->attributeCount = $attributeCount;
    }
    
    /**
     * Gets a human readable summary of this node.
     * 
     * @return string
     */
    public function __toString()
    {
        $parent = $this->parent;
        $this->parent = null;
        
        $string = print_r($this, true);
        
        $this->parent = $parent;
        
        return $string;
    }
    
    /**
     * Adds an attribute to this node.
     * 
     * @param string $name
     * @param string $value
     * 
     * @return XmlNode This node for method chaining.
     */
    public function addAttribute($name, $value)
    {
        $this->attributes[$name] = $value;
        return $this;
    }
    
    /**
     * Adds a child node to this node.
     * 
     * @param string $name
     * @param XmlNode $node
     * 
     * @return XmlNode This node for method chaining.
     */
    public function addChild($name, XmlNode $node)
    {
        $node->parent = $this;
        
        if (!isset($this->children[$name])) {
            $this->children[$name] = [$node];
            
        } else {
            $this->children[$name][] = $node;
        }
        
        return $this;
    }
    
    /**
     * Gets an attribute value of this node by name.
     * Returns an empty string if the attribute does not exist.
     * 
     * @param string $name
     * 
     * @return string
     */
    public function attribute($name)
    {
        if (isset($this->attributes[$name])) {
            return $this->attributes[$name];
        }
        return '';
    }
    
    /**
     * Gets the child nodes of this node.
     * 
     * @param string $name This will return only the children with the given
     *  name, or an empty array if none exist.
     * 
     * @return XmlNode[]
     */
    public function children($name = '')
    {
        if ($name && isset($this->children[$name])) {
            return $this->children[$name];
            
        } elseif ($name) {
            return [];
        }
        
        return $this->children;
    }
    
    /**
     * Finds a child with matching attribute values.
     * Returns null if there is no matching child.
     * 
     * @param string $child
     * @param string[] $attributes
     * 
     * @return XmlNode|null
     */
    public function childWithAttribute(
        $child,
        array $attributes
    ) {
        if (isset($this->children[$child])) {
            foreach ($this->children[$child] as $node) {
                $isMatch = true;
                foreach ($attributes as $name => $value) {
                    if ($node->attribute($name) != $value) {
                        $isMatch = false;
                        break;
                    }
                }
                if ($isMatch) {
                    return $node;
                }
            }
        }
        
        return null;
    }
    
    /**
     * Gets the depth of this node.
     * 
     * @return int
     */
    public function depth()
    {
        return $this->depth;
    }
    
    /**
     * Compares the name and attributes of this node with another and returns
     * true only if all the values are the same.
     * 
     * @param XmlNode $node
     * 
     * @return boolean
     */
    public function isEqual(XmlNode $node)
    {
        if ($this->name != $node->name()) {
            return false;
        }
        
        foreach ($this->attributes as $name => $value) {
            if ($node->attribute($name) != $value) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Gets the name of this node.
     * 
     * @return string
     */
    public function name()
    {
        return $this->name;
    }
    
    /**
     * Gets the type of this node as an integer constant as defined by the
     * constants in \XmlReader.
     * 
     * @return int
     */
    public function nodeType()
    {
        return $this->nodeType;
    }
    
    /**
     * Gets the type of this node as a human readable string.
     * 
     * @return string
     */
    public function nodeTypeName()
    {
        return $this->nodeTypeName;
    }
    
    /**
     * Gets the parent node of this node or null if there is not a parent node.
     * 
     * @return XmlNode|null
     */
    public function parent()
    {
        return $this->parent;
    }
    
    public function removeParent()
    {
        $this->parent = null;
        return $this;
    }
}

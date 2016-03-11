<?php

/*
 * Copyright (c) 2015 Greasy Lab.
 * 
 * Monday 16th November 2015
 * 
 * Tom Gray
 */

namespace GreasyLab\Library\Cli;

require_once 'GreasyLab/Library/Utils/StringUtils.php';

use GreasyLab\Library\Utils\StringUtils;

/**
 * Defines a parameter for a CLI command.
 *
 * @author Tom Gray
 * @version 1.0 Monday 16th November 2015
 */
class Parameter
{
    
    /**
     * A default value to use for this parameter if none is provided.
     * 
     * @var string
     */
    protected $defaultValue;
    
    /**
     * A description of this parameter.
     * 
     * @var string
     */
    protected $description;
    
    /**
     * Flag that defines whether this parameter is required or optional.
     * 
     * @var boolean
     */
    protected $isRequired;
    
    /**
     * The name of this parameter.
     * 
     * @var string
     */
    protected $name;
    
    /**
     * The data type of this parameter.
     * 
     * @var int
     */
    protected $type;
    
    /**
     * The value passed to the command for this parameter.
     * 
     * @var string
     */
    protected $value;
    
    /**
     * Construct a parameter definition.
     * 
     * @param string $name
     * @param string $description
     * @param int $type
     * @param boolean $isRequired
     * @param string $defaultValue
     * 
     * @throws \Exception
     */
    public function __construct(
        $name,
        $description,
        $type,
        $isRequired,
        $defaultValue = ''
    ) {
        $this->description = $description;
        $this->isRequired = $isRequired;
        $this->name = $name;
        $this->type = $type;
        
        if ($defaultValue && !$this->isValidArg($defaultValue)) {
            throw new \Exception(
                'The default value "' . $defaultValue . '" provided for "'
                . $name . '" is invalid'
            );
        } else {
            $this->value = $this->defaultValue = $defaultValue;
        }
    }
    
    /**
     * Get the description of this parameter.
     * 
     * @return string
     */
    public function description()
    {
        return $this->description;
    }
    
    /**
     * Get the name of this parameter.
     * 
     * @return string
     */
    public function name()
    {
        return $this->name;
    }
    
    /**
     * Get the value given for this parameter.
     * 
     * @return string
     */
    public function value()
    {
        return $this->value;
    }
    
    /**
     * Get whether this parameter is required.
     * 
     * @return boolean True if required, false if optional.
     */
    public function isRequired()
    {
        return $this->isRequired;
    }
    
    /**
     * Get whether a value is valid for this parameter definition.
     * 
     * @param string $value
     * 
     * @return boolean
     * 
     * @throws \Exception if the parameter type is not recognised.
     */
    public function isValidArg($value)
    {
        $isValid = false;
        
        switch ($this->type) {
            case ParameterType::INPUT_FILE:
                $isValid = is_file($value);
                break;
            case ParameterType::INPUT_DIR:
                $isValid = is_dir($value);
                break;
            case ParameterType::URL:
                // DO TG Feature: URL validation
                $isValid = true;
                break;
            case ParameterType::OUTPUT_FILE:
                // DO TG Feature: File path validation
                $isValid = true;
                break;
            case ParameterType::OUTPUT_DIR:
                if (is_file($value) || is_link($value)) {
                    throw new \Exception(
                        'Output directory name is an existing regular file or '
                        . 'link and cannot be created'
                    );
                } elseif (!is_dir($value)) {
                    if (!mkdir($value, 0744, true)) {
                        throw new \Exception(
                            'Output directory does not exist and cannot be '
                            . 'created'
                        );
                    }
                }
                $isValid = true;
                break;
            case ParameterType::YEAR_2_DIGITS:
                $isValid = StringUtils::isDigits($value, 2);
                break;
            case ParameterType::YEAR_4_DIGITS:
                $isValid = StringUtils::isDigits($value, 4);
                break;
            case ParameterType::INT:
                $isValid = is_numeric($value);
                break;
            case ParameterType::STRING:
                $isValid = true;
                break;
            case ParameterType::DOUBLE:
                // DO TG Implement: Validate a real number here.
                $isValid = true;
                break;
            case ParameterType::DATE_TIME:
                if (\DateTime::createFromFormat('Y-m-d H:i', $value)) {
                    $isValid = true;
                }
                break;
            default:
                throw new \Exception(
                    'Unrecognised parameter type in Parameter.php.'
                );
        }
        
        return $isValid;
    }
    
    /**
     * Set the value passed to the command for this parameter.
     * 
     * @param string $value
     * 
     * @return Parameter This parameter to allow method chaining.
     */
    public function setValue($value)
    {
        switch ($this->type) {
            case ParameterType::OUTPUT_DIR:
                $value = rtrim($value, '/').'/';
                break;
        }
        
        $this->value = $value;
        return $this;
    }
}

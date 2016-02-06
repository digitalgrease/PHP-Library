<?php

/*
 * Copyright (c) 2015 Greasy Lab.
 * 
 * Wednesday 25th November 2015
 * 
 * Tom Gray
 */

namespace GreasyLab\Library\Cli;

require_once 'Parameter.php';

use GreasyLab\Library\Cli\Parameter;

/**
 * Defines a collection of command line parameters.
 *
 * @author Tom Gray
 * @version 1.0 Wednesday 25th November 2015
 */
class Parameters
{
    /**
     * The defined parameters.
     * 
     * @var array
     */
    protected $parameters;
    
    /**
     * The error message if any parameters fail validation.
     * 
     * @var string
     */
    protected $error = '';
    
    /**
     * The minimum number of parameters that are required.
     * 
     * @var int
     */
    protected $minNumberOfArgs = 0;
    
    /**
     * Construct a collection of command line parameters.
     */
    public function __construct()
    {
        $this->parameters = [];
    }
    
    /**
     * Define and add a new parameter to this collection.
     * 
     * @param string $name
     * @param string $description
     * @param int $type
     * @param boolean $isRequired
     * @param string $defaultValue
     * 
     * @return Parameters This collection to allow method chaining.
     */
    public function add(
        $name,
        $description,
        $type,
        $isRequired,
        $defaultValue = ''
    ) {
        if ($isRequired) {
            ++$this->minNumberOfArgs;
        }
        
        $this->parameters[$name] = new Parameter(
            $name,
            $description,
            $type,
            $isRequired,
            $defaultValue
        );
        
        return $this;
    }
    
    /**
     * Get whether a set of command line arguments match the defined parameters.
     * 
     * @param array $args
     * 
     * @return boolean
     */
    public function areValidArgs(array $args)
    {
        // DO TG CliCommand: Improvement: Take the count of arguments into
        // account so that if a previous optional arg entered wrongly it is
        // flagged as not valid rather than a required arg that follows.
        // RemoveNullFiles with -e .gitignore is an example of this.
        
        $iArg = 0;
        $value = $args[$iArg];
        $areValidArgs = true;
        $isValidArg = false;
        
        foreach ($this->parameters as $parameter) {
            $isValidArg = $parameter->isValidArg($value);

            if ($parameter->isRequired() && !$isValidArg) {
                $this->error = $parameter->name().' is required and '.$value
                    .' is not a valid value';
                $areValidArgs = false;
                break;
            } elseif ($isValidArg) {
                $parameter->setValue($value);
                if (count($args) == ++$iArg) {
                    break;
                } else {
                    $value = $args[$iArg];
                }
            }
        }

        if ($areValidArgs && !$isValidArg) {
            $this->error = $value.' does not fit any parameters';
            $areValidArgs = false;
        }
        
        return $areValidArgs;
    }
    
    /**
     * Get the last error message.
     * 
     * @return string
     */
    public function error()
    {
        return $this->error;
    }
    
    /**
     * Get all the optional parameters that have been defined.
     * 
     * @return array
     */
    public function getOptionalParameters()
    {
        $optionalParameters = [];
        foreach ($this->parameters as $parameter) {
            if (!$parameter->isRequired()) {
                $optionalParameters[] = $parameter;
            }
        }
        return $optionalParameters;
    }
    
    /**
     * Get all the parameters that have been defined.
     * 
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }
    
    /**
     * Get all the required parameters that have been defined.
     * 
     * @return array
     */
    public function getRequiredParameters()
    {
        $requiredParameters = [];
        foreach ($this->parameters as $parameter) {
            if ($parameter->isRequired()) {
                $requiredParameters[] = $parameter;
            }
        }
        return $requiredParameters;
    }
    
    /**
     * Get the value of a parameter.
     * 
     * @param string $name The name of the parameter.
     * 
     * @return string
     */
    public function getValue($name)
    {
        return $this->parameters[$name]->value();
    }
    
    /**
     * Get whether any optional parameters have been defined.
     * 
     * @return boolean
     */
    public function hasOptionalParameters()
    {
        foreach ($this->parameters as $parameter) {
            if (!$parameter->isRequired()) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Get whether any parameters have been defined.
     * 
     * @return boolean
     */
    public function hasParameters()
    {
        return count($this->parameters) != 0;
    }
    
    /**
     * Get whether any required parameters have been defined.
     * 
     * @return boolean
     */
    public function hasRequiredParameters()
    {
        foreach ($this->parameters as $parameter) {
            if ($parameter->isRequired()) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Get the minimum number of parameters that are required.
     * 
     * @return int
     */
    public function minNumberOfArgs()
    {
        return $this->minNumberOfArgs;
    }
    
    /**
     * Get the number of parameters defined in this collection.
     * 
     * @return int
     */
    public function size()
    {
        return count($this->parameters);
    }
}

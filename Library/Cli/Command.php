<?php

/*
 * Copyright (c) 2015 Greasy Lab.
 * 
 * Monday 16th November 2015
 * 
 * Tom Gray
 */

namespace GreasyLab\Library\Cli;

require_once 'Flags.php';
require_once 'Parameters.php';
require_once 'GreasyLab/Library/Utils/Timer.php';

use GreasyLab\Library\Utils\Timer;

/**
 * Defines an abstract command run from the CLI.
 *
 * @author Tom Gray
 * @version 1.0 Monday 16th November 2015
 */
abstract class Command
{
    
    /**
     * The command line parameters defined for this command.
     * 
     * @var Parameters
     */
    protected $definedParams;
    
    /**
     * The command line flags defined for this command.
     * 
     * @var Flags
     */
    protected $definedFlags;
    
    /**
     * Description of what this command does.
     * 
     * @var string
     */
    protected $description = '';
    
    /**
     * Construct and configure a command.
     */
    public final function __construct()
    {
        $this->definedParams = new Parameters();
        $this->definedFlags = new Flags();
        $this->configure();
    }
    
    /**
     * Configure this command by defining a description, any parameters or other
     * settings. Is run before the run() method.
     * 
     * @return void
     */
    abstract protected function configure();
    
    /**
     * To be implemented to run this command.
     * 
     * @return int Zero if this command completed successfully, one if this
     *  command exited with errors.
     */
    abstract protected function run();
    
    /**
     * Display the command line arguments.
     * 
     * @param array $args
     * 
     * @return void
     */
    public final function displayArgs(array $args)
    {
        foreach ($args as $key => $arg) {
            $this->println('Arg '.$key.' => '.$arg);
        }
    }
    
    /**
     * Execute this command and run the user defined run method.
     * 
     * @param array $args The command line arguments.
     * 
     * @return int Zero if this command completed successfully, one if this
     *  command exited with errors.
     */
    public final function execute(array $args)
    {
        $exitStatus = 1;
        
        try {
            $timer = new Timer();
            if ($this->areValidArgs(array_slice($args, 1))) {
                $exitStatus = $this->run();
            } else {
                $this->displayHelp($args[0]);
            }
        } catch (\Exception $ex) {
            $this->println('An exception has been thrown: '.$ex->getMessage());
        }
        
        $this->println($timer->formatSeconds($timer->getElapsedTime()));
        
        return $exitStatus;
    }
    
    /**
     * Define a parameter for this command.
     * 
     * @param string $name
     * @param string $description
     * @param int $type
     * @param boolean $isRequired
     * @param string $defaultValue
     * 
     * @return Command This command to allow method chaining.
     */
    protected final function addParameter(
        $name,
        $description,
        $type,
        $isRequired = true,
        $defaultValue = ''
    ) {
        $this->definedParams->add(
            $name,
            $description,
            $type,
            $isRequired,
            $defaultValue
        );
        
        return $this;
    }
    
    /**
     * Define a flag for this command.
     * 
     * @param string $char
     * @param string $description
     * 
     * @return Command This command to allow method chaining.
     */
    protected final function addFlag(
        $char,
        $description
    ) {
        $this->definedFlags->add($char, $description);
        return $this;
    }
    
    /**
     * Get whether any command line arguments are valid.
     * 
     * @param array $args The command line arguments.
     * 
     * @return boolean True if the command line arguments fit those defined for
     *  this command, false otherwise.
     */
    protected final function areValidArgs(array $args)
    {
        $areValid = false;
        
        if (count($args) < $this->definedParams->minNumberOfArgs()) {
            $this->println('Not enough arguments');
        } elseif (count($args) > $this->maxNumberOfArgs()) {
            $this->println('Too many arguments');
        } elseif (count($args) > 0) {
            $areValid = $this->validateArgs($args);
        } else {
            $areValid = true;
        }
        
        return $areValid;
    }
    
    /**
     * Display text without a new line.
     * 
     * @param string $text
     * 
     * @return void
     */
    protected function display($text)
    {
        fwrite(STDOUT, $text);
    }
    
    /**
     * Display the command help.
     * 
     * @param string $commandPath The first command line argument that is the
     *  path to the command being run.
     * 
     * @return void
     */
    protected final function displayHelp($commandPath)
    {
        // Display command usage line.
        $command = pathinfo($commandPath, PATHINFO_FILENAME);
        $usageString = 'Usage: '.$command.' ';
        if ($this->definedFlags->hasFlags()) {
            $usageString .= '[-flags] ';
        }
        if ($this->definedParams->hasParameters()) {
            $usageString .= '[args...]';
        }
        $this->println();
        $this->println($usageString);
        
        // Display the possible flags.
        if ($this->definedFlags->hasFlags()) {
            $this->println();
            $this->println('where flags are:');
            foreach ($this->definedFlags->getFlags() as $flag) {
                /* @var $flag Flag */
                $this->println(
                    '    -'.$flag->char().'    '.$flag->description()
                );
            }
        }
        
        // Display the possible arguments.
        if ($this->definedParams->hasRequiredParameters()) {
            $this->println();
            $this->println('where required arguments are:');
            foreach ($this->definedParams->getRequiredParameters() as $param) {
                /* @var $param Parameter */
                $this->println(
                    '    <'.$param->name().'>'
                    .'    '.$param->description()
                );
            }
        }
        if ($this->definedParams->hasOptionalParameters()) {
            $this->println();
            $this->println('where optional arguments are:');
            foreach ($this->definedParams->getOptionalParameters() as $param) {
                /* @var $param Parameter */
                $this->println(
                    '    <'.$param->name().'>'
                    .'    '.$param->description()
                );
            }
        }
    }
    
    /**
     * Get the value of a defined parameter by name.
     * 
     * @param string $name
     * 
     * @return string
     */
    protected final function get($name)
    {
        return $this->definedParams->getValue($name);
    }
    
    /**
     * Get whether a flag has been set.
     * 
     * @param string $char
     * 
     * @return boolean
     */
    protected final function isFlagOn($char)
    {
        return $this->definedFlags->isFlagOn($char);
    }
    
    /**
     * Get the maximum number of arguments this command accepts.
     * 
     * @return int
     */
    protected final function maxNumberOfArgs()
    {
        $maxNumberOfArgs = $this->definedParams->size();
        if ($this->definedFlags->hasFlags()) {
            ++$maxNumberOfArgs;
        }
        return $maxNumberOfArgs;
    }
    
    /**
     * Display the given text and start a new line.
     *
     * @param string $text
     * @param bool $log
     * 
     * @return void
     */
    protected final function println($text = null, $log = false)
    {
        fwrite(STDOUT, $text . PHP_EOL);
        if ($log) {
            // DO TG * Implement logging here and in a separate method.
            $logFile = new LogFile(getcwd());
        }
    }
    
    /**
     * Set the description of this command.
     * 
     * @param string $description
     * 
     * @return Command This command to allow method chaining.
     */
    protected final function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }
    
    /**
     * Validate provided arguments against the defined flags and parameters.
     * 
     * @param array $args
     * 
     * @return boolean
     */
    protected final function validateArgs(array $args)
    {
        $iArg = 0;
        $value = $args[$iArg];
        $areValidArgs = true;
        
        // Validate any flags.
        if ($this->definedFlags->isFlagString($value)) {
            $areValidArgs = $this->definedFlags->isValidFlagString($value);
            ++$iArg;
        }
        
        // Validate any arguments.
        if ($areValidArgs && $iArg < count($args)) {
            $areValidArgs = $this->definedParams->areValidArgs(
                array_slice($args, $iArg)
            );
            if (!$areValidArgs) {
                $this->println($this->definedParams->error());
            }
        } elseif (!$areValidArgs) {
            $this->println($this->definedFlags->error());
        }
        
        return $areValidArgs;
    }
}

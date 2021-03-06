<?php

/*
 * Copyright (c) 2015 Digital Grease Limited.
 * 
 * Monday 16th November 2015
 * 
 * Tom Gray
 */

namespace DigitalGrease\Library\Cli;

require_once 'Controller.php';
require_once 'Flags.php';
require_once 'Parameters.php';
require_once 'DigitalGrease/Library/Utils/Stopwatch.php';

use DigitalGrease\Library\Utils\Stopwatch;

/**
 * Defines an abstract command run from the CLI.
 * 
 * DO TG Digital Grease: CLI: Feature: Add options in the form --dfsdf that can
 *  take one or more arguments.
 * DO TG Digital Grease: CLI: Feature: Allow an argument to be more than one
 *  type, e.g. input file or input directory.
 *
 * @author Tom Gray
 * @version 1.0 Monday 16th November 2015
 */
abstract class Command extends Controller
{
    
    const LOCK_FILE_NAME = 'lock.tmp';
    
    /**
     * Times the total running time of this command.
     * 
     * @var Stopwatch
     */
    protected $commandStopwatch;
    
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
            $this->commandStopwatch = new Stopwatch();
            
            if ($this->areValidArgs(array_slice($args, 1))) {
                $exitStatus = $this->run();
            } else {
                $this->displayHelp($args[0]);
            }
        } catch (\Exception $ex) {
            $this->println('An exception has been thrown: '.$ex->getMessage());
        }
        
        $this->println('Total run time: ' . $this->elapsedCommandTime());
        
        return $exitStatus;
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
     * Create a lock file that controls execution.
     * 
     * @return void
     * 
     * @throws \Exception
     */
    protected function createLockFile()
    {
        if (!touch(self::LOCK_FILE_NAME)) {
            throw new \Exception('Could not create lock file!');
        }
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
     * Get the current elapsed running time of this command as a human readable
     * string.
     * 
     * @return string
     */
    protected final function elapsedCommandTime()
    {
        return $this->commandStopwatch->formatSeconds(
            $this->commandStopwatch->elapsed()
        );
    }
    
    /**
     * Get the value of a defined parameter by name.
     * 
     * @param string $name
     * 
     * @return string
     */
    protected final function getArg($name)
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
     * Check whether a lock file exists.
     * 
     * @return boolean
     */
    protected function lockFileExists()
    {
        $isLockFile = is_file(self::LOCK_FILE_NAME);
        
//        if ($isLockFile) {
//            $this->println('Lock file exists');
//        } else {
//            $this->println('Lock file removed');
//        }
        
        return $isLockFile;
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
     * Get the current amount of memory that is being allocated to
     * this PHP script as a human readable string.
     * 
     * @return string
     */
    protected final function memoryUsage()
    {
        return memory_get_usage();
    }
    
    /**
     * Remove the lock file if it exists.
     * 
     * @return void
     */
    protected function removeLockFile()
    {
        if (is_file(self::LOCK_FILE_NAME)) {
            unlink(self::LOCK_FILE_NAME);
            $this->println('Lock file removed');
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
     * Get the split running time of this command since the last split or start
     * as a human readable string.
     * 
     * @return string
     */
    protected final function splitCommandTime()
    {
        return $this->commandStopwatch->formatSeconds(
            $this->commandStopwatch->split()
        );
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

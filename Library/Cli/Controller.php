<?php

/*
 * Copyright (c) 2016 Greasy Lab.
 * 
 * Monday 23rd May 2016
 * 
 * Tom Gray
 */

namespace GreasyLab\Library\Cli;

/**
 * Defines shared functionality for CLI controllers.
 *
 * @author Tom Gray
 */
abstract class Controller
{
    
    const SUCCESS = 0;
    
    const ERROR = 1;
    
    /**
     * Associative array that shares data and objects between the controllers.
     * 
     * @var array
     */
    protected static $container;
    
    /**
     * To be implemented to run this controller.
     * 
     * @return int Returns 0 if this controller completed successfully.
     *             Returns 1 if this controller exited with errors.
     */
    abstract protected function run();
    
    /**
     * Accept input from the user.
     * 
     * @param string $prompt A prompt to display to the user.
     * @param bool $isHidden True does not display the input on the screen.
     *                       False displays the input on screen.
     * 
     * @return string
     */
    protected final function acceptInput($prompt, $isHidden = false)
    {
        $this->println($prompt, false);
        
        if ($isHidden) {
            system('stty -echo');
        }
        
        $input = trim(fgets(STDIN));
        
        if ($isHidden) {
            system('stty echo');
        }
        
        return $input;
    }
    
    /**
     * Get the data associated with a key in the container.
     * 
     * @param string $key
     * 
     * @return mixed
     */
    protected final function get($key)
    {
        if (isset(self::$container[$key])) {
            return self::$container[$key];
        }
        throw new \Exception(
            'There is no data in the container associated with the key "'
            . $key . '"'
        );
    }
    
    /**
     * Display text on the screen.
     *
     * @param string $text
     * @param bool $newLine 
     * 
     * @return void
     */
    protected final function println($text = null, $newLine = true)
    {
        if ($newLine) {
            $text = $text . PHP_EOL;
        }
        
        fwrite(STDOUT, $text);
    }
    
    /**
     * Associate some data with a key in the container.
     * 
     * @param string $key
     * @param mixed $data
     * @param bool $final
     * 
     * @return void
     */
    protected final function set($key, $data, $final = true)
    {
        if (isset(self::$container[$key]) && $final) {
            throw new \Exception(
                'There is already data associated with the key "' . $key . '"'
            );
        }
        self::$container[$key] = $data;
    }
}

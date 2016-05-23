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
    
    /**
     * To be implemented to run this controller.
     * 
     * @return DO TG Decide on implementation and comment
     */
    abstract protected function run();
    
    /**
     * Accept input from the user.
     * 
     * @param string $prompt A prompt to display to the user.
     * @param bool $isHidden True does not display the input on the screen.
     *  False displays the input on screen.
     * 
     * @return string
     */
    protected function acceptInput($prompt, $isHidden = false)
    {
        $this->println($prompt);
        
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
}

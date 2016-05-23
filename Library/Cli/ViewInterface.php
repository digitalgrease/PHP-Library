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
 * Defines an API for a CLI view.
 * 
 * @author Tom Gray
 */
interface ViewInterface
{
    
    /**
     * Get the lines of text that make up the view.
     * 
     * @return array Array of strings which are the lines of the view.
     */
    public function getLines();
}

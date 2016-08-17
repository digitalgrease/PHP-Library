<?php

/*
 * Copyright (c) 2016 Digital Grease Limited.
 * 
 * Monday 23rd May 2016
 * 
 * Tom Gray
 */

namespace GreasyLab\Library\Cli;

require_once 'Controller.php';
require_once 'ViewInterface.php';

/**
 * Controller that displays a view.
 *
 * @author Tom Gray
 */
abstract class ViewController extends Controller
{
    
    /**
     * View to be displayed.
     * 
     * @var ViewInterface
     */
    protected $view;
    
    /**
     * Construct a controller.
     * 
     * @param ViewInterface $view
     */
    public function __construct(ViewInterface $view)
    {
        $this->view = $view;
    }
    
    /**
     * {@inheritdoc}
     */
    abstract protected function run();
    
    /**
     * Display the view.
     * 
     * @return void
     */
    protected function displayView()
    {
        foreach ($this->view->getLines() as $line) {
            $this->println($line);
        }
    }
}

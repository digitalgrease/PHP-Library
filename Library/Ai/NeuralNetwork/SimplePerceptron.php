<?php

/*
 * Copyright (c) 2016 Greasy Lab.
 * 
 * Wednesday 16th March 2016
 * 
 * Tom Gray
 */

namespace GreasyLab\Library\Ai\NeuralNetwork;

require_once 'Neuron.php';

/**
 * Implements a simple perceptron.
 * 
 * @version 1.0
 * @author Tom Gray
 */
class SimplePerceptron extends Neuron
{
    
    /**
     * {@inheritdoc}
     */
    protected function activationFunction($input)
    {
        if ($input > 0) {
            return 1;
        } else {
            return -1;
        }
    }
}

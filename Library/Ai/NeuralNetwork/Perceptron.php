<?php

/*
 * Copyright (c) 2016 Digital Grease Limited.
 * 
 * Wednesday 16th March 2016
 * 
 * Tom Gray
 */

namespace DigitalGrease\Library\Ai\NeuralNetwork;

require_once 'Neuron.php';

/**
 * Implements a perceptron.
 * 
 * @version 1.0
 * @author Tom Gray
 */
class Perceptron extends Neuron
{
    
    /**
     * @inheritDoc
     */
    protected function activationFunction($input)
    {
        if ($input > 0) {
            return [1];
        } else {
            return [0];
        }
    }
}

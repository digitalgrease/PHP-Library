<?php

/*
 * Copyright (c) 2016 Greasy Lab.
 * 
 * Wednesday 16th March 2016
 * 
 * Tom Gray
 */

namespace GreasyLab\Library\Ai\Neuron;

/**
 * A single neuron in an artificial neural network that sums its inputs.
 * 
 * @version 1.0
 * @author Tom Gray
 */
class SumInputNeuron extends AbstractNeuron
{
    
    /**
     * The neuron's activation function which sums the inputs.
     * 
     * @param array $inputs
     * 
     * @return float
     */
    protected function activationFunction(array $inputs)
    {
        $sum = 0;
        
        foreach ($inputs as $i) {
            $sum += $i;
        }
        
        return $sum;
    }
    
    /**
     * {@inheritdoc}
     */
    protected function outputFunction($input)
    {
        return $input;
    }
}

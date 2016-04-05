<?php

/*
 * Copyright (c) 2016 Greasy Lab.
 * 
 * Saturday 2nd April 2016
 * 
 * Tom Gray
 */

namespace GreasyLab\Library\Ai\NeuralNetwork;

/**
 * API for a neuron in a neural network.
 * 
 * @author Tom Gray
 */
interface NeuronInterface
{
    
    /**
     * 
     * 
     * @param array $inputs
     * @param float $delta
     */
    public function updateWeightsAndBias(array $inputs, $delta);
}

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
     * @param float $error
     * 
     * @return float
     */
    public function computeDelta($error);
    
    /**
     * Pass inputs through the network and obtain the output.
     * 
     * @param array $inputs
     * 
     * @return float
     */
    public function feedForward(array $inputs);
    
    /**
     * Train this network.
     * 
     * @param array $inputs
     * @param float $output The desired output.
     * 
     * @return void
     */
    public function train(array $inputs, $output);
    
    /**
     * 
     * 
     * @param array $inputs
     * @param float $error
     * 
     * @return float The delta of this neuron which is the error multiplied by
     *  the learning rate/constant of this neuron.
     */
    public function updateWeightsAndBias(array $inputs, $error);
}
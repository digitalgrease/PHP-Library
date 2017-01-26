<?php

/*
 * Copyright (c) 2016 Digital Grease Limited.
 * 
 * Version 1.0 Saturday 2nd April 2016
 * Version 2.0 Tuesday 24th January 2017
 * 
 * Tom Gray
 */

namespace DigitalGrease\Library\Ai\NeuralNetwork;

/**
 * API for a neuron in a neural network.
 * 
 * @version 2.0 Tuesday 24th January 2017
 * @author Tom Gray
 */
interface NeuronInterface extends NetworkInterface
{
    
    /**
     * Get the bias weight of this neuron.
     * 
     * @return float
     */
    public function bias();
    
    /**
     * 
     * @param float $error
     * 
     * @return float
     */
    public function computeDelta($error);
    
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
    
    /**
     * Get the weights of this neuron excluding the bias weight.
     * 
     * @return array
     */
    public function weightsWithoutBias();
}

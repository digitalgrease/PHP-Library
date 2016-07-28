<?php

/*
 * Copyright (c) 2016 Greasy Lab.
 * 
 * Thursday 31st March 2016
 * 
 * Tom Gray
 */

namespace GreasyLab\Library\Ai\NeuralNetwork;

/**
 * API for a neural network.
 * 
 * @version 1.0
 * @author Tom Gray
 */
interface NetworkInterface
{
    
    /**
     * Pass inputs through the network and obtain the output.
     * 
     * @param array $inputs
     * 
     * @return array
     */
    public function feedForward(array $inputs);
    
    /**
     * Train this network.
     * 
     * @param array $inputs
     * @param array $outputs The desired output.
     * 
     * @return void
     */
    public function train(array $inputs, array $outputs);
    
    /**
     * Get all the weights across the network.
     * 
     * @return array
     */
    public function weights();
}

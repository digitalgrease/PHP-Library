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
     * @return mixed
     */
    public function feedForward(array $inputs);
    
    /**
     * Train this network.
     * 
     * @param array $inputs
     * @param int $output The desired output.
     * 
     * @return bool True if the perceptron made a correct guess. False if an
     *  incorrect guess was made and the weights were adjusted.
     */
    public function train(array $inputs, $output);
}

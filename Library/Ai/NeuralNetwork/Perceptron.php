<?php

/*
 * Copyright (c) 2016 Greasy Lab.
 * 
 * Wednesday 16th March 2016
 * 
 * Tom Gray
 */

namespace GreasyLab\Library\Ai\NeuralNetwork;

/**
 * Provides basic functionality for a single perceptron in an artificial neural
 * network.
 * 
 * @version 1.0
 * @author Tom Gray
 */
class Perceptron
{
    
    /**
     * The bias weight.
     * 
     * @var float
     */
    protected $bias;
    
    /**
     * Multiplying the inputs by these weights gives the strengths of the
     * received signals.
     * 
     * @var array
     */
    protected $weights;
    
    /**
     * Construct the perceptron with the supplied weights.
     * 
     * @param array $weights
     */
    public function __construct(array $weights)
    {
        $this->weights = $weights;
    }
    
    /**
     * Pass a collection of inputs through this perceptron.
     * 
     * @param array $inputs
     * 
     * @return mixed
     */
    public function feedForward(array $inputs)
    {
        if (count($this->weights) == count($inputs)) {
            return $this->activationFunction($this->sumWeights($inputs));
        } else {
            throw new \Exception(
                'The number of inputs does not match the number of weights.'
            );
        }
    }
    
    /**
     * The perceptron's activation function.
     * 
     * @param float $input
     * 
     * @return mixed
     */
    protected function activationFunction($input)
    {
        if ($input > 0) {
            return 1;
        } else {
            return -1;
        }
    }
    
    /**
     * Sum the weights of the inputs.
     * 
     * @param array $inputs
     * 
     * @return float
     */
    protected function sumWeights(array $inputs)
    {
        $sum = $this->bias;
        $i = 0;
        foreach ($inputs as $input) {
            $sum += $this->weights[$i++] * $input;
        }
        return $sum;
    }
}

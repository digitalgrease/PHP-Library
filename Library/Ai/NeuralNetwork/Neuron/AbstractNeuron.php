<?php

/*
 * Copyright (c) 2016 Greasy Lab.
 * 
 * Wednesday 16th March 2016
 * 
 * Tom Gray
 */

namespace GreasyLab\Library\Ai\NeuralNetwork\Neuron;

/**
 * Provides basic functionality for a single neuron in an artificial neural
 * network.
 * 
 * @version 1.0
 * @author Tom Gray
 */
abstract class AbstractNeuron
{
    
    /**
     * Multiplying the inputs by these weights gives the strengths of the
     * received signals.
     * 
     * @var array
     */
    protected $weights;
    
    /**
     * Construct the neuron with the supplied weights.
     * 
     * @param array $weights
     */
    public function __construct(array $weights)
    {
        $this->weights = $weights;
    }
    
    /**
     * The neuron's activation function.
     * 
     * @param array $inputs
     * 
     * @return float
     */
    abstract protected function activationFunction(array $inputs);
    
    /**
     * The neuron's output (identity) function.
     * 
     * @param float $input
     * 
     * @return float
     */
    abstract protected function outputFunction($input);
    
    /**
     * Pass a collection of inputs through this neutron.
     * 
     * @param array $inputs
     * 
     * @return float
     */
    public function fire(array $inputs)
    {
        if (count($this->weights == count($inputs))) {
            return $this->outputFunction(
                $this->activationFunction(
                    $this->applyWeights($inputs)
                )
            );
        } else {
            throw new \Exception(
                'The number of inputs does not match the number of weights.'
            );
        }
    }
    
    /**
     * Apply the weights to the inputs.
     * 
     * @param array $inputs
     * 
     * @return array
     */
    private function applyWeights(array $inputs)
    {
        $outputs = [];
        
        $i = 0;
        foreach ($inputs as $input) {
            $outputs[] = $this->weights[$i++] * $input;
        }
        
        return $outputs;
    }
}

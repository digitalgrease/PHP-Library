<?php

/*
 * Copyright (c) 2016 Greasy Lab.
 * 
 * Thursday 17th March 2016
 * 
 * Tom Gray
 */

namespace GreasyLab\Library\Ai\NeuralNetwork;

/**
 * An artificial neural network.
 *
 * @version 1.0
 * @author Tom Gray
 */
class Network
{
    
    /**
     * 
     * 
     * @var array
     */
    protected $neurons;
    
    /**
     * Construct the network.
     * 
     * @param array $neurons
     */
    public function __construct(array $neurons)
    {
        $this->neurons = $neurons;
    }
    
    /**
     * Pass a set of inputs through the network and retrieve the result.
     * 
     * @param array $inputs
     * 
     * @return float
     */
    public function run(array $inputs)
    {
        if (count($inputs) == count($this->neurons[0])) {
            return $this->processHiddenLayers(
                $this->processFirstLayer($inputs)
            );
        } else {
            throw new Exception(
                'The number of inputs does not match the number of neurons in '
                . 'the first layer of the network.'
            );
        }
    }
    
    /**
     * DO TG ANN: Implement the training of a network.
     */
    public function train() {}
    
    /**
     * Process the inputs through the first layer of neutrons in the network.
     * 
     * @param array $inputs
     * 
     * @return array
     */
    private function processFirstLayer(array $inputs)
    {
        $outputs = [];
        foreach ($this->neurons[0] as $i => $neuron) {
            $outputs[] = $neuron->fire([$inputs[$i]]);
        }
        return $outputs;
    }
    
    /**
     * Process the outputs of the first layer of neutrons through the hidden
     * layers of the network.
     * 
     * @param array $inputs
     * 
     * @return array
     */
    private function processHiddenLayers(array $inputs)
    {
        for ($i = 1; $i < count($this->neurons); ++$i) {
            $outputs = [];
            foreach ($this->neurons[$i] as $neuron) {
                $outputs[] = $neuron->fire($inputs);
            }
            $inputs = $outputs;
        }
        return $outputs;
    }
}

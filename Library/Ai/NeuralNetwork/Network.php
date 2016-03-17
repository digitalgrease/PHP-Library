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
        
    }
    
    // DO TG ANN: Implement the training of a network.
    public function train() {}
    
    /**
     * DO TG *
     */
    private function processFirstLayer()
    {
        
        // Pass the inputs through the first layer.
        $outputs = [];
        foreach ($neurons[0] as $i => $neuron) {
            $this->println('Firing Layer #0');
            $outputs[] = $output = $neuron->fire([$inputs[$i]]);
            $this->println('Neuron #' . $i . ': ' . $inputs[$i] . ' => ' . $output);
        }
    }
    
    /**
     * DO TG **
     */
    private function processHiddenLayers()
    {
        
        // Pass the inputs through the hidden layers of the network.
        $inputs = $outputs;
        $this->println('Outputs and inputs for next layer are: ' . implode(', ', $inputs));
        for ($i = 1; $i < count($neurons); ++$i) {
            $this->println('Firing Layer #' . $i);
            $outputs = [];
            foreach ($neurons[$i] as $n => $neuron) {
                $outputs[] = $output = $neuron->fire($inputs);
                $this->println('Neuron #' . $i . ' ' . $n . ': ' . implode(', ', $inputs) . ' => ' . $output);
            }
            $inputs = $outputs;
            $this->println('Outputs and inputs for next layer are: ' . implode(', ', $inputs));
        }
    }
}

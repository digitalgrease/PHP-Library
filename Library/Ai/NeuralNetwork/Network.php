<?php

/*
 * Copyright (c) 2016 Greasy Lab.
 * 
 * Thursday 17th March 2016
 * 
 * Tom Gray
 */

namespace GreasyLab\Library\Ai\NeuralNetwork;

require_once 'NetworkInterface.php';

/**
 * An artificial neural network.
 *
 * @version 1.0
 * @author Tom Gray
 */
class Network implements NetworkInterface
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
     * @return mixed
     */
    public function feedForward(array $inputs)
    {
        if (count($inputs) == count($this->neurons[0])) {
            return $this->processHiddenLayers(
                $this->processFirstLayer($inputs)
            );
        } else {
            throw new \Exception(
                'The number of inputs does not match the number of neurons in '
                . 'the first layer of the network.'
            );
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function train(array $inputs, $output) {}
    
    /**
     * Process the inputs through the first layer of neutrons in the network.
     * 
     * @param array $inputs
     * 
     * @return array
     */
    private function processFirstLayer(array $inputs)
    {
        //echo 'First layer inputs = '.implode(',', $inputs).PHP_EOL;
        
        $outputs = [];
        foreach ($this->neurons[0] as $i => $neuron) {
            
            //echo 'Firing neuron '.$i.PHP_EOL;
            
            $outputs[] = $neuron->fire([$inputs[$i]]);
        }
        
        //echo 'First layer outputs = '.implode(',', $outputs).PHP_EOL;
        
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
        $outputs = $inputs;
        
        //echo 'Hidden layer inputs = '.implode(',', $inputs).PHP_EOL;
        
        for ($i = 1; $i < count($this->neurons); ++$i) {
            
            //echo 'Processing hidden layer #'.$i.PHP_EOL;
            
            $outputs = [];
            
            foreach ($this->neurons[$i] as $n => $neuron) {
                
                //echo 'Firing neuron '.$n.PHP_EOL;
                
                $outputs[] = $neuron->fire($inputs);
            }
            
            //echo 'Hidden layer #' .$i . ' outputs = '
            //.implode(',', $outputs).PHP_EOL;
            
            $inputs = $outputs;
        }
        
        //echo 'Final layer outputs = '.implode(',', $outputs).PHP_EOL;
        
        return $outputs;
    }
}

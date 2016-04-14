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
    
    protected $layerInputs;
    
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
     * Provide a string representation of this network.
     * 
     * @return string
     */
    public function __toString()
    {
        $str = '';
        
        foreach ($this->neurons as $layer => $neurons) {
            $str .= 'Layer #' . $layer . PHP_EOL;
            foreach ($neurons as $n => $neuron) {
                $str .= 'Neuron #' . $n . ': ' . $neuron . PHP_EOL;
            }
        }
        
        return $str;
    }
    
    /**
     * {@inheritdoc}
     */
    public function feedForward(array $inputs)
    {
        $this->layerInputs = [$inputs];
        
        //echo 'Hidden layer inputs = '.implode(',', $inputs).PHP_EOL;
        
        for ($layer = 0; $layer < count($this->neurons); ++$layer) {
            
            //echo 'Processing hidden layer #'.$i.PHP_EOL;
            
            $outputs = [];
            
            foreach ($this->neurons[$layer] as $neuron) {
                
                //echo 'Firing neuron '.$n.PHP_EOL;
                
                $outputs[] = $neuron->feedForward($inputs);
            }
            
            //echo 'Hidden layer #' .$i . ' outputs = '
            //.implode(',', $outputs).PHP_EOL;
            
            $this->layerInputs[] = $inputs = $outputs;
        }
        
        //echo 'Final layer outputs = '.implode(',', $outputs).PHP_EOL;
//        var_dump($this->layerInputs);
//        echo PHP_EOL;
        
        return $outputs;
    }
    
    /**
     * {@inheritdoc}
     */
    public function train(array $inputs, array $outputs)
    {
        
        // Process the inputs for the current guess.
        $guess = $this->feedForward($inputs);
//        echo 'Outputs: ' . print_r($this->layerInputs, true) . PHP_EOL;
        
        // Initialise storage for the errors and deltas.
        $layerErrors = [];
        $layerDeltas = [];
        for ($l = 1; $l < count($this->layerInputs); ++$l) {
            foreach ($this->layerInputs[$l] as $inputs) {
                $layerErrors[$l - 1][] = 0;
                $layerDeltas[$l - 1][] = 0;
            }
        }
        
        // Compute the errors of the final output.
        $lastLayer = count($this->neurons) - 1;
        for ($n = 0; $n < count($this->neurons[$lastLayer]); ++$n) {
            $layerErrors[$lastLayer][$n] = $outputs[$n] - $guess[$n];
        }
//        echo 'Errors: ' . print_r($layerErrors, true) . PHP_EOL;
        
        // Loop back through the layers and compute the errors and the deltas.
        for ($l = $lastLayer; $l > -1; --$l) {
            
            // Compute the delta for each neuron.
            foreach ($this->neurons[$l] as $n => $neuron) {
                $layerDeltas[$l][$n] = $neuron->computeDelta($layerErrors[$l][$n]);
                
                // Feed the deltas back through the weights of each neuron and
                // sum them to get the errors for the previous layer.
                if ($l) {
                    foreach ($neuron->weights() as $w => $weight) {
                        $layerErrors[$l - 1][$w] = $layerDeltas[$l][$n] * $weight;
                    }
                }
            }
//            echo 'Deltas: ' . print_r($layerDeltas, true) . PHP_EOL;
//            echo 'Errors: ' . print_r($layerErrors, true) . PHP_EOL;
        }
        
        // Update all the weights in the network.
        foreach ($this->neurons as $l => $layer) {
            foreach ($layer as $n => $neuron) {
                $neuron->updateWeightsAndBias(
                    $this->layerInputs[$l],
                    $layerErrors[$l][$n]
                );
            }
        }
    }
}

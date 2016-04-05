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
    
    protected $learningConstant;
    
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
    public function __construct(array $neurons, $learningConstant = 0.05)
    {
        $this->neurons = $neurons;
        $this->learningConstant = $learningConstant;
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
        $this->layerInputs = [$inputs];
        $outputs = $inputs;
        
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
        
        return $outputs;
    }
    
    /**
     * {@inheritdoc}
     */
    public function train(array $inputs, $output)
    {
        
        // Compute the overall network error.
        $guess = $this->feedForward($inputs);
        $error = $output - $guess[0];
        $delta = $error * $this->learningConstant;
        
        // Adjust the weights for the last layer.
        $lastLayer = count($this->neurons) - 1;
        foreach ($this->neurons[$lastLayer] as $neuron) {
            $neuron->updateWeightsAndBias(
                $this->layerInputs[$lastLayer],
                $delta
            );
        }
        
        // Perform the back propagation across the layers.
        for ($layer = $lastLayer - 1; $layer > -1; --$layer) {
            
            // Compute the error and delta for the layer.
            $error = 0;
            foreach ($this->neurons[$layer + 1] as $neuron) {
                $error += $neuron->bias() * $delta;
                foreach ($neuron->weights() as $weight) {
                    $error += $weight * $delta;
                }
            }
            $delta = $error * $this->learningConstant;
            
            // Update the weights of the neurons in the layer.
            foreach ($this->neurons[$layer] as $neuron) {
                $neuron->updateWeightsAndBias(
                    $this->layerInputs[$layer],
                    $delta
                );
            }
        }
        
        return $error == 0;
    }
}

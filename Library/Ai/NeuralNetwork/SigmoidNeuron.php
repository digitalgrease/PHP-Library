<?php

/*
 * Copyright (c) 2016 Greasy Lab.
 * 
 * Tuesday 5th April 2016
 * 
 * Tom Gray
 */

namespace GreasyLab\Library\Ai\NeuralNetwork;

require_once 'GreasyLab/Library/Utils/MathUtils.php';
require_once 'Neuron.php';

use GreasyLab\Library\Utils\MathUtils;

/**
 * Implements a sigmoid neuron.
 * 
 * @version 1.0
 * @author Tom Gray
 */
class SigmoidNeuron extends Neuron
{
    
    /**
     * {@inheritdoc}
     */
    protected function activationFunction($input)
    {
        $output = MathUtils::sigmoid($input);
        $gradient = MathUtils::sigmoidDerivative($output);
        
        $this->learningRate = max([$gradient, self::DEFAULT_LEARNING_RATE]);
        
        return $output;
    }
}
